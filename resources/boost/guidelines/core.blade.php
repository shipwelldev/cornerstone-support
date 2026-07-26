## Agent instructions

Do not edit `AGENTS.md` or `CLAUDE.md` directly. Laravel Boost generates these files from the guidelines supplied by the application and its dependencies, so direct changes will be overwritten.

Add or edit a focused source guideline under `.ai/guidelines` instead, then allow Laravel Boost to regenerate the configured agent instruction files.

## Coding standards

Follow `CODING_STANDARDS.md`. It is the authoritative application coding standard and overrides generated agent files, package guidance, framework examples, generic agent instructions, and individual preferences.

Agents cannot weaken Rules, enforcement, analysis, or suppressions. Agents must not disable a formatter rule, lower or exclude static analysis, alter an architecture check to accept a violation, add an ignore or baseline, or approve an exception. Only a human with the authority described by the standard may do so.

Agents follow Rules and Guidelines. An agent may depart from a Recommendation only when context clearly favors another approach, and must disclose the departure and rationale. A human must explicitly authorize every Guideline override.

## Skills

Application-owned agent skills belong under `.ai/skills`. Treat this directory as the source of truth; do not manually copy or edit published versions in agent-specific directories.

Laravel Boost publishes skills to the appropriate agent-specific paths according to the developers' configured agents in `boost.json`.

## Verification

Only consider implementation work complete after both canonical workflows pass in this order:

1. Run `composer fix`, review every correction, and resolve unintended changes.
2. Run `composer verify` and fix every failing non-correcting gate.

If a later fix changes code, restart verification from `composer fix`.
