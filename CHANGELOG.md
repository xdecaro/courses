# Changelog

## 1.5.0 — 2026-09-09

### Added
- Optional Editor by xdecaro support for the public course description through Joomla's standard `editor` form field.
- The preferred editor is `decaroeditor`; Joomla's built-in `none` editor remains the fallback when Editor by xdecaro is absent or disabled.

### Compatibility
- Editor remains optional and Courses does not import Editor private PHP classes or access Editor private storage.
- The existing `safehtml` filter, course save flow, ACL and CSRF behavior are unchanged.
- Edition notes remain a normal textarea and no Courses database schema changes are required.
- `com_decarocourses`, `pkg_decarocourses` and the historical `Xdecaro\Component\Decarocourses` namespace remain unchanged.

## 1.4.0 — 2026-09-09

### Added
- Optional bridges to Notifications and Tasks using only their documented component services.
- ACL-protected Analytics provider for Courses metrics and datasets.
- Joomla Scheduled Tasks reminders for edition registration closure and edition start, only when a manager Joomla user is explicitly configured.
- Core capability declarations for Analytics, Notifications, Tasks and reminder processing.

### Changed
- Corrected Joomla SQL manifest charset metadata to `utf8`; table definitions remain `utf8mb4`.
- Package now contains the Analytics provider and Scheduler plugin; fresh installs enable them, updates preserve administrator plugin state.

### Compatibility
- No Courses or Editions table changes.
- Core, Notifications, Tasks and Analytics remain optional for the relevant integration surface.
- `com_decarocourses`, `pkg_decarocourses` and the historical `Xdecaro\Component\Decarocourses` namespace remain unchanged.

## 1.3.0 — 2026-09-09

### Changed

- Courses now consumes the canonical Core by xdecaro PHP namespace `xdecaro\Core` for cross-product references and optional shared UI assets.
- Core-dependent integration features now explicitly require Core by xdecaro 1.3.0 or newer.
- The administrator Information view continues to fall back to the local Courses UI when Core is absent, too old or unavailable.

### Compatibility

- `com_decarocourses`, `pkg_decarocourses`, database tables and the historical Courses namespace `Xdecaro\Component\Decarocourses` remain unchanged.
- Core remains optional for Courses as a whole; only features that invoke the Core integration adapter require Core >= 1.3.0.
- No database schema changes; the 1.3.0 SQL file is only a Joomla schema-version marker.
- Existing Courses, Editions, Forms integration, ACL and application workflows are unchanged.

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
