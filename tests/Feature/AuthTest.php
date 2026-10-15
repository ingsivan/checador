<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Entrar');
    }

    public function test_employee_can_log_in_and_out(): void
    {
        $user = User::factory()->create(['email' => 'ana@checador.test']);

        $this->post('/login', ['email' => 'ana@checador.test', 'password' => 'password'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
        $this->get('/')->assertOk()->assertSee($user->name);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        User::factory()->create(['email' => 'ana@checador.test']);

        $this->from('/login')
            ->post('/login', ['email' => 'ana@checador.test', 'password' => 'nope'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_employee_cannot_log_in(): void
    {
        User::factory()->inactive()->create(['email' => 'ex@checador.test']);

        $this->post('/login', ['email' => 'ex@checador.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
