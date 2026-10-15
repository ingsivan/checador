<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'user_id', 'title', 'description', 'status', 'position', 'due_date'])]
class Task extends Model
{
    use HasFactory;

    /**
     * Kanban columns, in board order.
     *
     * @var array<string, string>
     */
    public const STATUSES = [
        'todo' => 'Pendiente',
        'doing' => 'En progreso',
        'review' => 'En revisión',
        'done' => 'Terminado',
    ];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
