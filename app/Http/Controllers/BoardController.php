<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $projectId = $request->integer('proyecto') ?: null;
        $mine = $request->boolean('mias');

        $tasks = Task::query()
            ->with(['project', 'user'])
            ->whereHas('project', fn ($q) => $q->active())
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($mine, fn ($q) => $q->where('user_id', $request->user()->id))
            ->orderBy('position')
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('status');

        return view('board', [
            'columns' => collect(Task::STATUSES)->map(fn (string $label, string $key) => [
                'key' => $key,
                'label' => $label,
                'tasks' => $tasks->get($key, collect()),
            ]),
            'projects' => Project::active()->orderBy('name')->get(),
            'people' => User::active()->orderBy('name')->get(),
            'projectId' => $projectId,
            'mine' => $mine,
        ]);
    }
}
