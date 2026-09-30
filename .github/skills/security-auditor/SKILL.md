---

name: security-auditor
description: Performs evidence-based security reviews of application code, configuration, dependencies, data flows, and access controls, prioritizing realistic risks and practical remediation.
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# Security Auditor

Use this skill when reviewing code, architecture, configuration, dependencies, authentication, authorization, data handling, or other security-sensitive changes.

## Core Principles

1. **Understand the system first**

   * Identify the application's trust boundaries, users, roles, sensitive data, external integrations, and important data flows.
   * Inspect relevant project conventions and security requirements before making recommendations.
   * Do not report theoretical issues without considering whether the affected component actually exists or is reachable.

2. **Protect secrets**

   * Detect hardcoded passwords, API keys, tokens, private keys, connection credentials, and other secrets.
   * Prefer environment variables or the project's established secret-management mechanism.
   * Never expose secrets in logs, error messages, source control, responses, screenshots, or generated documentation.

3. **Validate untrusted input**

   * Treat user input, uploaded files, external API data, headers, cookies, and client-controlled identifiers as untrusted.
   * Validate input according to its expected type, format, range, and business constraints.
   * Use context-appropriate output encoding to prevent injection and cross-site scripting.
   * Do not blindly sanitize or transform data when validation or contextual escaping is the appropriate control.

4. **Prevent injection**

   * Use parameterized queries, ORM bindings, safe APIs, and framework-provided protections.
   * Review SQL, command execution, template rendering, filesystem operations, redirects, and other interpreter boundaries.
   * Never construct executable commands or queries from untrusted input without appropriate controls.

5. **Verify authentication and authorization**

   * Confirm that protected operations require appropriate authentication.
   * Verify authorization for every sensitive operation, including reads as well as writes.
   * Check ownership and resource access to prevent IDOR/BOLA-style vulnerabilities.
   * Do not rely solely on client-side restrictions.

6. **Protect sensitive data**

   * Minimize collection, storage, transmission, and exposure of sensitive information.
   * Review API responses, logs, exceptions, database records, URLs, and client-side state for unnecessary disclosure.
   * Never expose credentials, authentication secrets, or sensitive internal data merely for debugging convenience.

7. **Review application security controls**
   Consider controls relevant to the system, including:

   * CSRF protection
   * session security
   * password handling
   * rate limiting
   * secure file uploads
   * path traversal protection
   * SSRF prevention
   * secure redirects
   * clickjacking protections
   * security headers
   * error handling
   * audit logging
   * dependency and package risks
   * secure production configuration

8. **Respect framework security mechanisms**

   * Prefer established framework protections over custom security mechanisms.
   * Do not disable security middleware, validation, authorization, escaping, or other protections merely to simplify development.
   * If a protection must be bypassed, document why and assess the resulting risk.

9. **Prioritize findings**
   Classify findings according to realistic impact and exploitability:

   * `CRITICAL` — severe compromise or highly sensitive impact with a credible attack path
   * `HIGH` — significant compromise or sensitive-data/access impact
   * `MEDIUM` — meaningful security weakness with limited or conditional impact
   * `LOW` — minor weakness or defense-in-depth improvement

   Explain the affected component, attack path or condition when relevant, impact, and recommended remediation.

10. **Verify remediation**

    * Re-check the affected behavior after fixes.
    * Run relevant tests and security checks.
    * Confirm that the remediation addresses the root cause rather than hiding the symptom.

## AI Slop Prevention

* Do not invent vulnerabilities without evidence.
* Do not recommend security controls that are irrelevant to the application.
* Do not introduce custom security abstractions when established framework mechanisms are sufficient.
* Do not weaken validation, authorization, or security controls to make functionality work.
* Do not expose real secrets while demonstrating a vulnerability or fix.
* Do not treat every theoretical vulnerability as an equally important production risk.
