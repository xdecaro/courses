# Changelog

## 1.1.0 — 2026-09-08

### Added

- Optional Xdecaro Core integration adapter registered through Joomla dependency injection.
- Support for the Core `EntityReference` and `RelationReference` cross-product contracts when Core is available.
- Controlled failure when a Core-dependent integration is requested without a compatible Core installation.

### Compatibility

- Xdecaro Core remains optional in this release.
- No database schema changes.
- Existing Courses, Editions and Forms integration behavior is unchanged.
