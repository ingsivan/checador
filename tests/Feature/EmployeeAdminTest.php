<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_developers_cannot_manage_employees(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/employees')
            ->assertForbidden();
    }

    public function test_admin_sees_employee_list(): void
    {
        $admin = User::factory()->admin()->create();
        $dev = User::factory()->create(['name' => 'Carlos Méndez']);

        $this->actingAs($admin)
            ->get('/admin/employees')
            ->assertOk()
            ->assertSee('Carlos Méndez');
    }

    public function test_admin_can_add_employee(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/employees', [
            'name' => 'Daniela Ruiz',
            'email' => 'daniela@checador.test',
            'position' => 'Backend',
            'role' => 'developer',
            'work_start' => '09:30',
            'work_end' => '18:30',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertRedirect('/admin/employees');

        $this->assertDatabaseHas('users', [
            'email' => 'daniela@checador.test',
            'role' => 'developer',
            'is_active' => true,
        ]);
    }

    public function test_end_time_must_be_after_start_time(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/employees', [
            'name' => 'X',
            'email' => 'x@checador.test',
            'role' => 'developer',
            'work_start' => '18:00',
            'work_end' => '09:00',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertSessionHasErrors('work_end');
    }

    public function test_admin_can_edit_without_changing_password(): void
    {
        $admin = User::factory()->admin()->create();
        $dev = User::factory()->create();
        $hash = $dev->password;

        $this->actingAs($admin)->put("/admin/employees/{$dev->id}", [
            'name' => 'Nuevo Nombre',
            'email' => $dev->email,
            'role' => 'developer',
            'work_start' => '08:00',
            'work_end' => '17:00',
            'is_active' => '1',
        ])->assertRedirect('/admin/employees');

        $dev->refresh();
        $this->assertSame('Nuevo Nombre', $dev->name);
        $this->assertSame($hash, $dev->password);
    }

    public function test_admin_can_deactivate_others_but_not_self(): void
    {
        $admin = User::factory()->admin()->create();
        $dev = User::factory()->create();

        $this->actingAs($admin)->patch("/admin/employees/{$dev->id}/toggle")->assertRedirect();
        $this->assertFalse($dev->fresh()->is_active);

        $this->actingAs($admin)->patch("/admin/employees/{$admin->id}/toggle")->assertStatus(422);
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_cannot_demote_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put("/admin/employees/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'developer',
            'work_start' => '09:00',
            'work_end' => '18:00',
            'is_active' => '0',
        ]);

        $this->assertTrue($admin->fresh()->isAdmin());
        $this->assertTrue($admin->fresh()->is_active);
    }
}
