# Xdecaro Core integration

Courses uses the Xdecaro Core cross-product reference contract for relationships with other Xdecaro products.

Current Joomla component element: `com_decarocourses`.

Use:

- `Xdecaro\Core\Integration\EntityReference` for `component/entity/id` references;
- `Xdecaro\Core\Integration\RelationReference` for typed links between references.

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
