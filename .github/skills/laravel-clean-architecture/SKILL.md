---

name: laravel-clean-architecture
description: Applies pragmatic Laravel architecture and framework conventions while keeping application code simple, testable, maintainable, and appropriately separated by responsibility.
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# Laravel Architecture & Engineering Guidelines

Use this skill when developing or refactoring Laravel applications.

## Core Principles

1. **Follow Laravel conventions first**

   * Prefer established Laravel mechanisms and conventions before introducing custom architecture.
   * Use framework features when they solve the problem clearly.
   * Do not recreate framework functionality unnecessarily.

2. **Keep responsibilities clear**

   * Controllers should coordinate HTTP concerns rather than contain large amounts of business logic.
   * Models should represent domain data and relationships without becoming unmaintainable god objects.
   * Views/components should focus on presentation and interaction.
   * Place business logic in the simplest appropriate location.

3. **Use abstractions only when justified**

   * Actions, services, repositories, domain objects, and other layers may be introduced when complexity, reuse, testing needs, or domain boundaries justify them.
   * Do not create an Action or Service for every controller method by default.
   * Do not introduce repository patterns solely to wrap straightforward Eloquent queries.

4. **Validation**

   * Use Form Requests for substantial or reusable HTTP validation.
   * Keep simple validation inline when it is clearer and appropriate.
   * Separate validation from business rules when the rules require domain-level reasoning.

5. **Authorization**

   * Enforce authorization on the server.
   * Use Laravel's established authorization mechanisms such as policies and gates where appropriate.
   * Verify resource ownership and permissions for sensitive operations.

6. **Eloquent and database access**

   * Define meaningful relationships explicitly.
   * Use appropriate casts and mass-assignment protection.
   * Prefer Eloquent and query builder features when they provide clear, maintainable solutions.
   * Avoid raw SQL unless it provides a justified benefit such as a database-specific operation or significant query optimization.
   * Watch for N+1 queries and unnecessary database work.

7. **Database schema**

   * Use migrations as the source of truth for schema changes.
   * Never modify an already-applied migration to represent a new schema change; create a new migration instead.
   * Add appropriate foreign keys, constraints, indexes, and data types.
   * Preserve data integrity at the database level where practical.

8. **HTTP and API design**

   * For APIs, use consistent resource representations and validation/error conventions appropriate to the application.
   * Use API Resources when they improve control and consistency of JSON representations.
   * Do not impose API-specific architecture on applications that do not expose an API.

9. **Configuration and environment**

   * Keep environment-specific values in configuration/environment mechanisms.
   * Never hardcode secrets or deployment-specific credentials.
   * Avoid reading environment variables directly throughout application logic when Laravel configuration is appropriate.

10. **Error handling**

    * Allow Laravel's normal exception handling to operate where appropriate.
    * Handle expected domain or application errors explicitly when the user needs a meaningful response.
    * Do not catch broad exceptions merely to suppress errors.
    * Never expose internal stack traces or sensitive implementation details to end users in production.

11. **Testing**

    * Add tests for meaningful business behavior and important regressions.
    * Prefer Laravel's established testing tools and patterns.
    * Test behavior and contracts rather than implementation details where practical.

12. **Performance**

    * Optimize based on actual or reasonably expected bottlenecks.
    * Use eager loading, appropriate indexes, pagination, caching, queues, and query optimization when justified.
    * Do not add caching, queues, or infrastructure complexity without a concrete need.

## Architecture Discipline

Prefer the simplest architecture that remains clear and maintainable.

For example, a straightforward feature may reasonably use:

```text
Route
  ↓
Controller / Livewire Component
  ↓
Model / Query
```

A complex feature may justify:

```text
Route
  ↓
Controller
  ↓
Action / Domain Service
  ↓
Models / Queries
```

Do not force every feature into the second structure.

## AI Slop Prevention

* Do not create unnecessary repositories, services, interfaces, DTOs, traits, or abstraction layers.
* Do not create API architecture for a non-API application.
* Do not introduce microservices or external infrastructure to solve ordinary Laravel problems.
* Do not rewrite working Laravel code merely to make it look more "enterprise."
* Prefer readable Laravel code over architectural ceremony.
* Preserve existing project conventions unless there is a concrete reason to change them.
