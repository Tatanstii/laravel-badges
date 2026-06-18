<?php

namespace Insynnia\Badges;

use Illuminate\Support\Facades\DB;
use Insynnia\Badges\Models\Badge;
use Insynnia\Badges\Models\Entity;
use Insynnia\Badges\Models\EntityBadge;
use Insynnia\Badges\Models\Trigger;

class BadgeService
{
    /**
     * Fire a trigger by slug and award its badge to the entity.
     *
     * @param  array<string, mixed>  $metadata
     * @return array{awarded: bool, badge: ?Badge, reason: string}
     */
    public function fireTrigger(
        string $triggerSlug,
        string $externalEntityId,
        array $metadata = []
    ): array {
        $trigger = Trigger::bySlug($triggerSlug)
            ->with('badge')
            ->first();

        if (! $trigger) {
            return [
                'awarded' => false,
                'badge' => null,
                'reason' => "Trigger '{$triggerSlug}' not found or inactive.",
            ];
        }

        $badge = $trigger->badge;

        if (! $badge || ! $badge->is_active || $badge->trashed()) {
            return [
                'awarded' => false,
                'badge' => null,
                'reason' => 'Badge associated with this trigger is inactive.',
            ];
        }

        $entity = Entity::findOrCreateByExternalId($externalEntityId);

        $award = DB::transaction(function () use ($entity, $badge, $trigger, $metadata) {
            return EntityBadge::award(
                entityId: $entity->id,
                badgeId: $badge->id,
                triggerId: $trigger->id,
                awardedBy: 'trigger',
                metadata: $metadata,
            );
        });

        if (! $award) {
            return [
                'awarded' => false,
                'badge' => $badge,
                'reason' => 'Badge already awarded to this entity.',
            ];
        }

        return [
            'awarded' => true,
            'badge' => $badge,
            'reason' => 'Badge awarded successfully.',
        ];
    }

    /**
     * Manually award a badge by id, bypassing triggers.
     *
     * @param  array<string, mixed>  $metadata
     * @return array{awarded: bool, badge: ?Badge, reason: string}
     */
    public function awardManually(
        string $badgeId,
        string $externalEntityId,
        array $metadata = []
    ): array {
        $badge = Badge::active()->find($badgeId);

        if (! $badge) {
            return [
                'awarded' => false,
                'badge' => null,
                'reason' => 'Badge not found or inactive.',
            ];
        }

        $entity = Entity::findOrCreateByExternalId($externalEntityId);

        $award = EntityBadge::award(
            entityId: $entity->id,
            badgeId: $badge->id,
            awardedBy: 'manual',
            metadata: $metadata,
        );

        if (! $award) {
            return [
                'awarded' => false,
                'badge' => $badge,
                'reason' => 'Badge already awarded.',
            ];
        }

        return [
            'awarded' => true,
            'badge' => $badge,
            'reason' => 'Badge awarded manually.',
        ];
    }

    /**
     * Revoke a badge from an entity. Returns true when a record was removed.
     */
    public function revoke(string $badgeId, string $externalEntityId): bool
    {
        $entity = Entity::where('external_id', $externalEntityId)->first();

        if (! $entity) {
            return false;
        }

        return EntityBadge::where('entity_id', $entity->id)
            ->where('badge_id', $badgeId)
            ->delete() > 0;
    }
}
