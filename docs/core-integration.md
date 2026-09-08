# Xdecaro Core integration

Courses uses the Xdecaro Core cross-product reference contract for relationships with other Xdecaro products.

Current Joomla component element: `com_decarocourses`.

From Courses 1.3.0, new Core API calls use the canonical lowercase namespace introduced by Core 1.3.0:

- `xdecaro\Core\Integration\EntityReference` for `component/entity/id` references;
- `xdecaro\Core\Integration\RelationReference` for typed links between references.

The deprecated `Xdecaro\Core` compatibility namespace shipped by Core 1.3.0 is not used by Courses 1.3.0.

## Optional runtime adapter

`Xdecaro\Component\Decarocourses\Administrator\Service\CoreIntegrationService` is registered in the Joomla DI container.

Core remains optional for Courses as a whole. Core-dependent integration calls require Core by xdecaro 1.3.0 or newer:

- `isAvailable()` verifies the canonical Core namespace, minimum version and public reference classes;
- `createEntityReference()` creates a Courses-owned reference;
- `createRelationReference()` creates a typed relation from a Courses entity to another product's published entity;
- requesting a Core-dependent reference without Core 1.3.0+ produces a controlled `RuntimeException` rather than an opaque fatal error.

Courses remains the owner of courses, editions, enrollments, lessons, attendance and evaluations.

Typical integrations include:

- Membership member -> Courses enrollment with relation type such as `participant`;
- Forms submission -> Courses enrollment with relation type such as `source_submission`;
- Documents document -> course, edition or enrollment with a document-specific relation type;
- Events event -> course edition, lesson or examination event;
- Membership identity may be reused without moving membership lifecycle into Courses.

Never infer another product's table name from an EntityReference and never write directly to another product's private tables as the integration mechanism.

Entity type names become stable public API only when Courses explicitly publishes them. Keep internal model/table names private until then.

Optional integrations must fail gracefully when the other product is unavailable. Avoid circular mandatory dependencies.
