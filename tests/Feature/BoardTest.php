<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_board_shows_tasks_by_column_and_filters(): void
    {
        $user = User::factory()->create();
        $web = Project::create(['name' => 'Web', 'color' => 'indigo']);
        $app = Project::create(['name' => 'App', 'color' => 'rose']);
        Task::create(['project_id' => $web->id, 'user_id' => $user->id, 'title' => 'Hacer login', 'status' => 'doing']);
        Task::create(['project_id' => $app->id, 'title' => 'Pantalla de perfil', 'status' => 'todo']);

        $this->actingAs($user)->get('/tablero')->assertOk()->assertSee('Hacer login')->assertSee('Pantalla de perfil');
        $this->actingAs($user)->get("/tablero?proyecto={$app->id}")->assertDontSee('Hacer login')->assertSee('Pantalla de perfil');
        $this->actingAs($user)->get('/tablero?mias=1')->assertSee('Hacer login')->assertDontSee('Pantalla de perfil');
    }

    public function test_anyone_can_create_edit_and_delete_tasks(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['name' => 'Web', 'color' => 'indigo']);

        $this->actingAs($user)->post('/tareas', [
            'title' => 'Nueva tarea', 'project_id' => $project->id, 'user_id' => $user->id, 'status' => 'todo',
        ])->assertRedirect();
        $task = Task::sole();

        $this->put("/tareas/{$task->id}", [
            'title' => 'Renombrada', 'project_id' => $project->id, 'status' => 'review',
        ])->assertRedirect();
        $this->assertSame('review', $task->fresh()->status);
        $this->assertNull($task->fresh()->user_id);

        $this->delete("/tareas/{$task->id}")->assertRedirect();
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_drag_and_drop_moves_and_reorders(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['name' => 'Web', 'color' => 'indigo']);
        $a = Task::create(['project_id' => $project->id, 'title' => 'A', 'status' => 'doing', 'position' => 0]);
        $b = Task::create(['project_id' => $project->id, 'title' => 'B', 'status' => 'todo', 'position' => 0]);

        $this->actingAs($user)
            ->patchJson("/tareas/{$b->id}/mover", ['status' => 'doing', 'order' => [$b->id, $a->id]])
            ->assertOk();

        $this->assertSame('doing', $b->fresh()->status);
        $this->assertSame(0, $b->fresh()->position);
        $this->assertSame(1, $a->fresh()->position);
    }

    public function test_tasks_cannot_go_to_archived_projects(): void
    {
        $project = Project::create(['name' => 'Viejo', 'color' => 'indigo', 'is_active' => false]);

        $this->actingAs(User::factory()->create())
            ->post('/tareas', ['title' => 'X', 'project_id' => $project->id, 'status' => 'todo'])
            ->assertSessionHasErrors('project_id');
    }

    public function test_only_admin_manages_projects(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/projects')->assertForbidden();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post('/admin/projects', ['name' => 'Portal', 'color' => 'emerald', 'is_active' => '1'])
            ->assertRedirect('/admin/projects');
        $this->actingAs($admin)->get('/admin/projects')->assertSee('Portal');
    }
}
