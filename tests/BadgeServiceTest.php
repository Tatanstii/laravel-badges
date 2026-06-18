<?php

use Insynnia\Badges\BadgeService;
use Insynnia\Badges\Models\Badge;
use Insynnia\Badges\Models\Entity;
use Insynnia\Badges\Models\EntityBadge;
use Insynnia\Badges\Models\Trigger;

it('awards a badge once and is idempotent', function () {
    $badge = Badge::factory()->create();
    $entity = Entity::factory()->create();

    $first = EntityBadge::award($entity->id, $badge->id);
    $second = EntityBadge::award($entity->id, $badge->id);

    expect($first)->not->toBeNull()
        ->and($second)->toBeNull()
        ->and(EntityBadge::count())->toBe(1);
});

it('fires a trigger and awards its badge', function () {
    $trigger = Trigger::factory()->create();
    $service = new BadgeService;

    $result = $service->fireTrigger($trigger->slug, 'user_42');

    expect($result['awarded'])->toBeTrue()
        ->and($result['badge']->id)->toBe($trigger->badge_id)
        ->and(Entity::where('external_id', 'user_42')->exists())->toBeTrue();
});

it('does not award when the trigger badge is inactive', function () {
    $badge = Badge::factory()->inactive()->create();
    $trigger = Trigger::factory()->create(['badge_id' => $badge->id]);

    $result = (new BadgeService)->fireTrigger($trigger->slug, 'user_7');

    expect($result['awarded'])->toBeFalse();
});

it('revokes an awarded badge', function () {
    $badge = Badge::factory()->create();
    $entity = Entity::factory()->create();
    EntityBadge::award($entity->id, $badge->id);

    $revoked = (new BadgeService)->revoke($badge->id, $entity->external_id);

    expect($revoked)->toBeTrue()
        ->and(EntityBadge::count())->toBe(0);
});
