<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ClockTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_full_day_in_order(): void
    {
        $user = User::factory()->create(['work_start' => '09:00']);
        $this->actingAs($user);

        Carbon::setTestNow('2026-10-14 09:05:00');
        $this->post('/checar', ['step' => 'check_in_at'])->assertRedirect('/');
        $this->get('/')->assertSee('Tiempo trabajado hoy')->assertSee('Salgo a comer');

        Carbon::setTestNow('2026-10-14 14:00:00');
        $this->post('/checar', ['step' => 'lunch_out_at']);
        $this->get('/')->assertSee('Ya regresé de comer')->assertSee('de 60 min');

        Carbon::setTestNow('2026-10-14 15:10:00');
        $this->post('/checar', ['step' => 'lunch_in_at'])->assertSessionHas('status', 'Bienvenido de vuelta. Comiste 70 min.');

        Carbon::setTestNow('2026-10-14 18:05:00');
        $this->post('/checar', ['step' => 'check_out_at']);
        $this->get('/')->assertSee('Jornada terminada');

        $day = Attendance::sole();
        $this->assertSame(70, $day->lunchMinutes());
        $this->assertSame(9 * 60 - 70, $day->workedMinutes());
        $this->assertTrue($day->isOverLunch());
        $this->assertSame(0, $day->lateMinutes()); // 5 min is inside the tolerance
    }

    public function test_steps_cannot_be_skipped_or_repeated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/checar', ['step' => 'lunch_out_at'])->assertSessionHas('error');
        $this->assertDatabaseCount('attendances', 0);

        $this->post('/checar', ['step' => 'check_in_at']);
        $this->post('/checar', ['step' => 'check_in_at'])->assertSessionHas('error');
        $this->post('/checar', ['step' => 'lunch_in_at'])->assertSessionHas('error');
    }

    public function test_lunch_is_optional(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/checar', ['step' => 'check_in_at']);
        $this->post('/checar', ['step' => 'check_out_at'])->assertSessionHas('status');

        $this->assertNotNull(Attendance::sole()->check_out_at);
    }

    public function test_late_arrival_is_measured_after_tolerance(): void
    {
        $user = User::factory()->create(['work_start' => '09:00']);
        Carbon::setTestNow('2026-10-14 09:30:00');

        $this->actingAs($user)->post('/checar', ['step' => 'check_in_at']);

        $this->assertSame(20, Attendance::sole()->lateMinutes());
    }

    public function test_team_and_history_pages(): void
    {
        $ana = User::factory()->create(['name' => 'Ana López']);
        $carlos = User::factory()->create(['name' => 'Carlos Méndez']);
        Attendance::create(['user_id' => $carlos->id, 'date' => today()->toDateString(), 'check_in_at' => now()->subHours(4), 'lunch_out_at' => now()->subMinutes(20)]);

        $this->actingAs($ana)->get('/equipo')
            ->assertOk()
            ->assertSee('Carlos Méndez')
            ->assertSee('Comiendo · 1', false);

        $this->actingAs($carlos)->get('/historial')->assertOk()->assertSee('Salida a comer');
    }

    public function test_help_page_is_public(): void
    {
        $this->get('/como-funciona')->assertOk()->assertSee('Así funciona Checador');
    }
}
