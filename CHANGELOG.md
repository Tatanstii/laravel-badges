# Changelog

All notable changes to `insynnia/laravel-badges` will be documented in this file.

## v1.0.0 - Unreleased

### Added

- Initial release: `Badge`, `Trigger`, `Entity` and `EntityBadge` models.
- `BadgeService` with `fireTrigger()`, `awardManually()` and `revoke()`.
- Idempotent badge awarding (unique per entity + badge).
- Publishable config and migrations.
- Factories and a Pest test suite.
