<p align="center">
  <h1 align="center">🏅 Laravel Badges</h1>
  <p align="center">A small, framework-native badge &amp; achievement engine for Laravel.<br>Define <strong>badges</strong>, expose named <strong>triggers</strong>, and award them to <strong>entities</strong> — idempotently.</p>
</p>

<p align="center">
  <a href="https://github.com/Tatanstii/laravel-badges/releases"><img alt="Latest Version" src="https://img.shields.io/github/v/tag/Tatanstii/laravel-badges?style=flat-square&label=version"></a>
  <a href="https://github.com/Tatanstii/laravel-badges/actions/workflows/run-tests.yml"><img alt="Tests" src="https://img.shields.io/github/actions/workflow/status/Tatanstii/laravel-badges/run-tests.yml?branch=main&label=tests&style=flat-square"></a>
  <a href="https://github.com/Tatanstii/laravel-badges/stargazers"><img alt="Stars" src="https://img.shields.io/github/stars/Tatanstii/laravel-badges?style=flat-square"></a>
  <img alt="PHP Version" src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-11%20|%2012%20|%2013-FF2D20?style=flat-square&logo=laravel">
  <a href="LICENSE"><img alt="License" src="https://img.shields.io/badge/license-MIT-green?style=flat-square"></a>
</p>

---

No multi-tenancy, no billing, no dashboard. Just the badge logic — drop it into any Laravel app and start awarding.

## ✨ Features

- 🎖️ **Badges** with name, description, image, category and tier (`bronze` → `platinum`).
- ⚡ **Triggers** — award a badge by firing a named slug (`first-login`, `100-sales`, …).
- 🪪 **Entities** keyed by *your* `external_id`, auto-created on first award.
- ♻️ **Idempotent awards** — firing twice never duplicates.
- 🗂️ **Metadata** — attach arbitrary JSON to badges, entities and awards.
- 🧩 **Bring your own HTTP** — ships the domain logic, stays unopinionated about routes & auth.
- 🧪 Fully tested, portable across SQLite / MySQL / PostgreSQL.

## 📦 Requirements

- PHP 8.2+
- Laravel 11, 12 or 13

## 🚀 Installation

```bash
composer require insynnia/laravel-badges
php artisan migrate
```

Publish config / migrations if you want to tweak them:

```bash
php artisan vendor:publish --tag=badges-config
php artisan vendor:publish --tag=badges-migrations
```

## 🧠 Concepts

| Model         | What it is                                                          |
|---------------|---------------------------------------------------------------------|
| `Badge`       | A named award (name, description, image, category, tier, metadata). |
| `Trigger`     | A slug that, when fired, awards a specific badge.                    |
| `Entity`      | A recipient, identified by your own `external_id` (e.g. a user id). |
| `EntityBadge` | The award record linking an entity to a badge (unique per pair).    |

```
fireTrigger('first-login', 'user_123')
        │
        ▼
   Trigger(slug) ──▶ Badge ──▶ EntityBadge ◀── Entity(external_id)
                                   (unique entity + badge)
```

## ⚡ Quick start

```php
use Insynnia\Badges\BadgeService;
use Insynnia\Badges\Models\Badge;
use Insynnia\Badges\Models\Trigger;

$badge = Badge::create(['name' => 'First Login', 'tier' => 'bronze']);
Trigger::create(['badge_id' => $badge->id, 'slug' => 'first-login']);

$badges = app(BadgeService::class);

// Fire a trigger — auto-creates the entity, awards the badge once.
$result = $badges->fireTrigger('first-login', externalEntityId: 'user_123');
// ['awarded' => true, 'badge' => Badge, 'reason' => 'Badge awarded successfully.']

// Award directly by badge id (bypasses triggers).
$badges->awardManually($badge->id, 'user_123');

// Revoke.
$badges->revoke($badge->id, 'user_123');
```

## 📖 API reference

`BadgeService` is the entry point. `fireTrigger()` and `awardManually()` both
return `array{awarded: bool, badge: ?Badge, reason: string}` and are
idempotent — a repeat award returns `awarded => false`.

| Method                                                | Description                                                  |
|-------------------------------------------------------|--------------------------------------------------------------|
| `fireTrigger(string $slug, string $entityId, array $metadata = [])` | Fire a trigger by slug and award its badge.     |
| `awardManually(string $badgeId, string $entityId, array $metadata = [])` | Award a badge by id, bypassing triggers.   |
| `revoke(string $badgeId, string $entityId): bool`     | Remove an award. Returns `true` if a record was deleted.     |

### Querying

```php
Badge::active()->byCategory('streak')->byTier('gold')->get();
$entity->badges;                       // badges an entity holds
Badge::find($id)->entities;            // entities that hold a badge
$badge->image_url;                     // resolved via the configured disk
```

### Badge images

`image_url` resolves `image_path` through the disk in `config/badges.php`
(defaults to `public`). Store the file yourself and persist its path:

```php
$badge->update(['image_path' => $path]); // $badge->image_url is now resolvable
```

## 🌐 HTTP layer

This package ships the domain logic only — no routes or controllers — so it
stays unopinionated about your API shape, auth and pagination. Wire
`BadgeService` into your own controllers. A typical mapping:

| Method & path                          | Call                                   |
|----------------------------------------|----------------------------------------|
| `POST /trigger/{slug}`                 | `fireTrigger($slug, $entityId, $meta)` |
| `POST /entities/{id}/badges`           | `awardManually($badgeId, $id, $meta)`  |
| `DELETE /entities/{id}/badges/{badge}` | `revoke($badgeId, $id)`                |

## 🧪 Testing

```bash
composer install
composer test
```

## 🤝 Contributing

PRs welcome. Please add a test for any behaviour change and run `composer test`
before opening a pull request.

## 🔒 Security

If you discover a security issue, please email the maintainers instead of using
the issue tracker.

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for what has changed recently.

## 📄 License

The MIT License (MIT). See [LICENSE](LICENSE).
