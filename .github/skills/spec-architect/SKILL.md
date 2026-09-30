---

name: spec-architect
description: Analyzes requirements and designs implementation plans for non-trivial software changes, identifying affected components, contracts, dependencies, risks, and verification before implementation.
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# Specification & Architecture Planning

Use this skill when a request involves a new feature, significant behavior change, architectural change, database or schema modification, integration, migration, or other work where implementation mistakes could create substantial rework.

## Core Principles

1. **Understand the requirement**

   * Identify the requested outcome, constraints, assumptions, and acceptance criteria.
   * Separate explicit requirements from inferred implementation details.
   * Do not invent requirements to fill gaps.

2. **Inspect existing context**
   Before planning, inspect relevant:

   * project documentation
   * repository structure
   * existing implementation patterns
   * configuration
   * database schema
   * routes/interfaces
   * tests
   * dependencies
   * related features

3. **Prefer existing patterns**

   * Reuse established project conventions when appropriate.
   * Avoid introducing a new architectural pattern when an existing one already solves the problem.
   * Do not redesign unrelated parts of the system.

4. **Define the impact boundary**
   Identify likely:

   * files/components to create
   * files/components to modify
   * files/components to remove
   * database/schema changes
   * API or interface changes
   * configuration changes
   * dependencies
   * tests
   * documentation

5. **Define contracts and data flow**
   When relevant, document:

   * inputs and outputs
   * validation rules
   * state transitions
   * authorization requirements
   * database relationships
   * external service interactions
   * error behavior
   * compatibility considerations

6. **Identify risks and edge cases**
   Consider:

   * existing behavior that must remain unchanged
   * failure states
   * concurrency
   * data integrity
   * security
   * backwards compatibility
   * migration/deployment concerns
   * unusual but valid inputs

7. **Create an implementation sequence**
   Break complex work into logical phases that can be implemented and verified independently.

8. **Choose the appropriate planning depth**

   * For small, well-understood changes, use lightweight planning and proceed.
   * For complex, risky, cross-cutting, or irreversible changes, present the plan before implementation.
   * When the user explicitly requests a plan or approval checkpoint, always provide it before making changes.

9. **Define verification**
   Specify how the implementation will be validated:

   * automated tests
   * manual verification
   * type checks
   * static analysis
   * database verification
   * integration testing
   * UI verification
   * security checks

## AI Slop Prevention

* Do not create architecture diagrams or plans merely for appearance.
* Do not introduce layers, services, repositories, abstractions, or dependencies without a concrete reason.
* Do not expand the requested feature into unrelated functionality.
* Do not design for hypothetical requirements unless explicitly requested.
* Do not modify files outside the established impact boundary without justification.
* Do not treat a speculative assumption as a confirmed requirement.

## Output for Complex Work

When a substantial plan is warranted, provide:

1. Objective
2. Existing context
3. Proposed approach
4. Impacted components
5. Data/contract changes
6. Implementation phases
7. Risks and edge cases
8. Verification strategy
9. Open questions or assumptions

Then obtain approval before implementing if the change is sufficiently complex or the user requested an approval checkpoint.
