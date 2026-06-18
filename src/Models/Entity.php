<?php

namespace Insynnia\Badges\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Insynnia\Badges\Database\Factories\EntityFactory;

class Entity extends Model
{
    use HasFactory, HasUuids;

    /** @var list<string> */
    protected $fillable = ['external_id', 'display_name', 'avatar_url', 'metadata'];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function entityBadges(): HasMany
    {
        return $this->hasMany(EntityBadge::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'entity_badges')
            ->withPivot(['trigger_id', 'awarded_by', 'metadata', 'awarded_at'])
            ->withTimestamps();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function findOrCreateByExternalId(
        string $externalId,
        array $attributes = []
    ): self {
        return self::firstOrCreate(
            ['external_id' => $externalId],
            $attributes
        );
    }

    protected static function newFactory(): EntityFactory
    {
        return EntityFactory::new();
    }
}
