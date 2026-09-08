# Changelog

## 1.2.0 — 2026-09-08

### Added

- Optional Core by xdecaro 1.1+ Web Asset Manager integration on the administrator Information view.
- Core design-token bridge for the existing Courses `dc-*` Information UI.
- Dedicated Core layout wrapper using `.xdecaro-scope` only when Core UI assets are available.

### Compatibility

- Core by xdecaro remains optional; Courses keeps the existing local Information layout and CSS when Core is absent or incompatible.
- No database schema changes; the 1.2.0 SQL file is only a Joomla schema-version marker.
- Courses, Editions, Forms integration, ACL and application workflows are unchanged.

## 1.1.0 — 2026-09-08

### Added

- Optional Core by xdecaro integration adapter registered through Joomla dependency injection.
- Support for the Core `EntityReference` and `RelationReference` cross-product contracts when Core is available.
- Controlled failure when a Core-dependent integration is requested without a compatible Core installation.

### Compatibility

- Core by xdecaro remains optional in this release.
- No database schema changes.
- Existing Courses, Editions and Forms integration behavior is unchanged.
