<?php

namespace Insynnia\Badges\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Insynnia\Badges\Database\Factories\BadgeFactory;

class Badge extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /** @var list<string> */
    protected $fillable = ['name', 'description', 'image_path', 'category', 'tier', 'metadata', 'is_active'];

    /** @var list<string> */
    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function triggers(): HasMany
    {
        return $this->hasMany(Trigger::class);
    }

    public function entityBadges(): HasMany
    {
        return $this->hasMany(EntityBadge::class);
    }

    public function entities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class, 'entity_badges')
            ->withPivot(['trigger_id', 'awarded_by', 'metadata', 'awarded_at'])
            ->withTimestamps();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk(config('badges.disk'))->url($this->image_path);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeByTier(Builder $query, string $tier): Builder
    {
        return $query->where('tier', $tier);
    }

    protected static function newFactory(): BadgeFactory
    {
        return BadgeFactory::new();
    }
}
