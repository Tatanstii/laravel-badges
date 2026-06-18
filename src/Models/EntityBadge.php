<?php

namespace Insynnia\Badges\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityBadge extends Model
{
    use HasFactory, HasUuids;

    /** @var list<string> */
    protected $fillable = ['entity_id', 'badge_id', 'trigger_id', 'awarded_by', 'metadata', 'awarded_at'];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'awarded_at' => 'datetime',
        ];
    }

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function trigger(): BelongsTo
    {
        return $this->belongsTo(Trigger::class);
    }

    /**
     * Award a badge to an entity. Idempotent — returns null if the entity
     * already holds the badge.
     *
     * @param  array<string, mixed>  $metadata
     */
    public static function award(
        string $entityId,
        string $badgeId,
        ?string $triggerId = null,
        string $awardedBy = 'trigger',
        array $metadata = [],
    ): ?self {
        $existing = self::where('entity_id', $entityId)
            ->where('badge_id', $badgeId)
            ->exists();

        if ($existing) {
            return null;
        }

        return self::create([
            'entity_id' => $entityId,
            'badge_id' => $badgeId,
            'trigger_id' => $triggerId,
            'awarded_by' => $awardedBy,
            'metadata' => $metadata,
            'awarded_at' => now(),
        ]);
    }
}
