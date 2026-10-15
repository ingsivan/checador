<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['position'] = (int) Task::where('status', $data['status'])->max('position') + 1;

        Task::create($data);

        return back()->with('status', 'Tarea agregada al tablero.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $task->update($this->validated($request));

        return back()->with('status', 'Tarea actualizada.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return back()->with('status', 'Tarea eliminada.');
    }

    /**
     * Drag and drop: put the task in a column and renumber that column in the order received.
     */
    public function move(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Task::STATUSES))],
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        $task->update(['status' => $data['status']]);

        foreach (array_values($data['order']) as $position => $id) {
            Task::whereKey($id)->where('status', $data['status'])->update(['position' => $position]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'project_id' => ['required', Rule::exists('projects', 'id')->where('is_active', true)],
            'user_id' => ['nullable', Rule::exists('users', 'id')->where('is_active', true)],
            'status' => ['required', Rule::in(array_keys(Task::STATUSES))],
            'due_date' => ['nullable', 'date'],
        ]);

        // The form always sends every field; a missing optional one means "clear it".
        return $data + ['description' => null, 'user_id' => null, 'due_date' => null];
    }
}
