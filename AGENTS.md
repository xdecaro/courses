# Courses — Codex Repository Rules

## Xdecaro Core integration

Courses is part of the Xdecaro Joomla ecosystem and should use **Xdecaro Core** from the beginning for infrastructure that is genuinely shared across multiple Xdecaro extensions.

Core is infrastructure, not Courses business logic.

Before implementing a reusable technical feature, check whether the same responsibility belongs in Core or is already provided by a stable Core API.

Good Core candidates include:

- shared design tokens and `.xdecaro-*` UI primitives;
- light/dark mode foundations;
- responsive administrator UI helpers;
- common buttons, badges, cards, tables, modals, alerts and loading states;
- shared Web Asset Manager registration;
- generic JavaScript utilities;
- AJAX/CSRF helpers that remain Joomla-compliant;
- dependency/version checks;
- common diagnostics;
- Xdecaro extension registry and shared information/update UI.

Keep all Courses-specific business logic in this repository, including:

- course definitions;
- editions;
- enrollments;
- lessons;
- attendance;
- evaluations;
- teachers, coordinators and students when represented by Courses domain logic;
- certificates and course-specific workflows;
- course-specific reporting and exports.

Maintain the separation between course, edition, enrollment, lesson, attendance and evaluation. Do not move these domain concepts into Core.

Do not move code into Core merely because it could technically be reused. A Core abstraction must be domain-neutral and useful to more than one product.

## Migration and implementation rule

When a task touches functionality that is a good Core candidate:

1. inspect the current Courses implementation first;
2. inspect the available Core public API before designing a duplicate;
3. use Core directly when a stable API already covers the requirement;
4. keep Courses-specific behavior in Courses;
5. avoid circular dependencies: Courses may depend on Core, Core must never depend on Courses;
6. preserve existing data and update paths;
7. avoid unnecessary compatibility wrappers;
8. verify desktop, tablet, smartphone, light mode and dark mode when UI is affected;
9. test the real Courses workflows that use the changed shared functionality.

Because Courses is newer than Forms, prefer building new shared infrastructure on Core rather than creating a second local implementation that will immediately need migration.

If Core is not available in the current workspace or the required public API does not yet exist, do not invent a fake Core API. Implement only what is necessary locally, keeping the reusable boundary clear so it can be moved safely later.

## Dependency policy

If Courses declares Core as a mandatory runtime dependency, the package and installer/update path must define and enforce a documented minimum compatible Core version.

Dependency handling must be predictable:

- clean install must explain a missing dependency clearly;
- updates must not destroy existing data or configuration;
- version incompatibility must produce a controlled administrator message rather than an opaque fatal error;
- package manifests and release artifacts must stay coherent.

Do not silently assume a Core API exists without verifying it.

## Public API stability

Treat Core public classes, services, asset identifiers, JavaScript APIs, CSS classes and CSS variables as stable contracts.

Do not copy internal Core implementation details into Courses and do not access Core internals that are not part of its public API.

## Joomla and security

Core integration must not weaken Courses security or Joomla conventions.

Continue to enforce where relevant:

- server-side ACL;
- Joomla CSRF tokens;
- filtered and validated input;
- escaped output;
- bound database queries;
- safe upload validation;
- no authorization decisions made only in JavaScript.

## Regression rule

A Core-related change is complete only when the affected Courses behavior remains verified.

Check as applicable:

- clean installation;
- update installation;
- Joomla 4/5/6 where the implementation supports them;
- administrator/frontend behavior;
- courses and editions;
- enrollments;
- lessons and attendance;
- evaluations;
- assets loaded once;
- AJAX;
- ACL and CSRF;
- PHP errors/warnings;
- JavaScript console;
- desktop/tablet/smartphone;
- light/dark mode.

Do not combine an opportunistic Core integration with unrelated large refactors.

When the user says **“procedi”**, execute the requested work directly after inspecting the relevant code and dependencies. Do not ask for another confirmation when the requirements are already clear.
