<?php

namespace Insynnia\Badges\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Insynnia\Badges\Database\Factories\TriggerFactory;

class Trigger extends Model
{
    use HasFactory, HasUuids;

    /** @var list<string> */
    protected $fillable = ['badge_id', 'slug', 'description', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function entityBadges(): HasMany
    {
        return $this->hasMany(EntityBadge::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug)->where('is_active', true);
    }

    protected static function newFactory(): TriggerFactory
    {
        return TriggerFactory::new();
    }
}
