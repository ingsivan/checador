<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-10-15 12:00:00'); // Thursday
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function day(User $user, string $date, string $in, string $out, string $back, ?string $leave = '18:00'): void
    {
        Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'check_in_at' => "$date $in",
            'lunch_out_at' => "$date $out",
            'lunch_in_at' => "$date $back",
            'check_out_at' => $leave ? "$date $leave" : null,
        ]);
    }

    public function test_ranking_orders_by_average_lunch(): void
    {
        $admin = User::factory()->admin()->create();
        $ana = User::factory()->create(['name' => 'Ana López', 'work_start' => '09:00']);
        $carlos = User::factory()->create(['name' => 'Carlos Méndez', 'work_start' => '09:00']);

        $this->day($ana, '2026-10-12', '09:00', '14:00', '14:45');
        $this->day($carlos, '2026-10-12', '09:30', '14:00', '15:20'); // late + 80 min lunch
        $this->day($carlos, '2026-10-13', '09:00', '14:00', '14:40', null); // forgot to leave

        $response = $this->actingAs($admin)->get('/admin/reportes?semana=2026-10-14')->assertOk();

        $ranking = $response->viewData('ranking');
        $this->assertSame('Carlos Méndez', $ranking[0]['user']->name);
        $this->assertSame(60, $ranking[0]['avg_lunch']);
        $this->assertSame(1, $ranking[0]['over_days']);
        $this->assertSame(1, $ranking[0]['late_days']);
        $this->assertSame(1, $ranking[0]['open_days']);
        $this->assertSame(8 * 60 + 30 - 80, $ranking[0]['worked']); // the open day adds nothing
        $this->assertSame(45, $ranking[1]['avg_lunch']);
        $response->assertSee('¿Quién tarda más en comer?');
    }

    public function test_csv_export(): void
    {
        $admin = User::factory()->admin()->create();
        $ana = User::factory()->create(['name' => 'Ana López']);
        $this->day($ana, '2026-10-12', '09:00', '14:00', '14:45');

        $csv = $this->actingAs($admin)->get('/admin/reportes/csv?semana=2026-10-12')->assertOk()->streamedContent();

        $this->assertStringContainsString('"Ana López",1,8.3,45,45,0,0,0', $csv);
    }

    public function test_reports_are_admin_only(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/reportes')->assertForbidden();
    }

    public function test_admin_fixes_forgotten_check_out(): void
    {
        $admin = User::factory()->admin()->create();
        $ana = User::factory()->create();
        $this->day($ana, '2026-10-13', '09:00', '14:00', '15:00', null);

        $this->actingAs($admin)->get("/admin/asistencias/{$ana->id}?semana=2026-10-13")->assertOk()->assertSee('Sin salida');

        $this->put("/admin/asistencias/{$ana->id}/2026-10-13", [
            'check_in_at' => '09:00', 'lunch_out_at' => '14:00', 'lunch_in_at' => '15:00', 'check_out_at' => '18:30',
        ])->assertSessionHas('status');

        $this->assertSame('18:30', Attendance::sole()->check_out_at->format('H:i'));
    }

    public function test_admin_cannot_save_marks_out_of_order(): void
    {
        $admin = User::factory()->admin()->create();
        $ana = User::factory()->create();

        $this->actingAs($admin)->put("/admin/asistencias/{$ana->id}/2026-10-13", [
            'check_in_at' => '09:00', 'lunch_out_at' => '08:00',
        ])->assertSessionHasErrors('lunch_out_at');

        $this->assertDatabaseCount('attendances', 0);
    }
}
