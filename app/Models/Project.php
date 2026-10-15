<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'client', 'color', 'description', 'is_active'])]
class Project extends Model
{
    use HasFactory;

    /**
     * Palette offered for projects. Each maps to Tailwind classes in the views.
     *
     * @var array<string, string>
     */
    public const COLORS = [
        'indigo' => 'Índigo',
        'emerald' => 'Esmeralda',
        'amber' => 'Ámbar',
        'rose' => 'Rosa',
        'sky' => 'Cielo',
        'violet' => 'Violeta',
        'orange' => 'Naranja',
        'teal' => 'Turquesa',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
