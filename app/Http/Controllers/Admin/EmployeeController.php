<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $employees = User::query()
            ->when($request->string('q')->trim()->isNotEmpty(), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.employees.index', [
            'employees' => $employees,
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.employees.form', ['employee' => new User([
            'role' => User::ROLE_DEVELOPER,
            'work_start' => '09:00',
            'work_end' => '18:00',
            'is_active' => true,
        ])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        User::create($data);

        return redirect()->route('admin.employees.index')
            ->with('status', "Se agregó a {$data['name']}.");
    }

    public function edit(User $employee): View
    {
        return view('admin.employees.form', ['employee' => $employee]);
    }

    public function update(Request $request, User $employee): RedirectResponse
    {
        $data = $this->validated($request, $employee);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        // An admin can't lock themselves out by demoting or deactivating their own account.
        if ($employee->is($request->user())) {
            $data['role'] = User::ROLE_ADMIN;
            $data['is_active'] = true;
        }

        $employee->update($data);

        return redirect()->route('admin.employees.index')
            ->with('status', "Se guardaron los cambios de {$employee->name}.");
    }

    public function toggle(Request $request, User $employee): RedirectResponse
    {
        abort_if($employee->is($request->user()), 422, 'No puedes desactivar tu propia cuenta.');

        $employee->update(['is_active' => ! $employee->is_active]);

        $state = $employee->is_active ? 'activado' : 'desactivado';

        return back()->with('status', "Se ha {$state} a {$employee->name}.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?User $employee = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($employee)],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_DEVELOPER])],
            'work_start' => ['required', 'date_format:H:i'],
            'work_end' => ['required', 'date_format:H:i', 'after:work_start'],
            'password' => [$employee ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ]);

        $data['is_active'] = $request->boolean('is_active', $employee === null);

        return $data;
    }
}
