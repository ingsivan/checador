<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('admin.projects.index', [
            'projects' => Project::withCount([
                'tasks',
                'tasks as done_count' => fn ($q) => $q->where('status', 'done'),
            ])->orderByDesc('is_active')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.form', ['project' => new Project(['color' => 'indigo', 'is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $project = Project::create($this->validated($request));

        return redirect()->route('admin.projects.index')->with('status', "Se creó el proyecto {$project->name}.");
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.form', ['project' => $project]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $project->update($this->validated($request));

        return redirect()->route('admin.projects.index')->with('status', "Se guardó {$project->name}.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'client' => ['nullable', 'string', 'max:255'],
            'color' => ['required', Rule::in(array_keys(Project::COLORS))],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
