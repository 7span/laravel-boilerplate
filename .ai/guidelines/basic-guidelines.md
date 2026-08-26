## General

Don't tell me I'm right all the time. Be critical, we're equals. Stay neutral and objective.

Don't overuse emojis.

## Plan Mode

In Plan mode, think through the full approach before writing any code. Confirm it with me first.

For non-trivial tasks, save the finalized plan to docs/plans/ as YYYY-MM-DD-short-description.md (e.g., 2026-03-19-user-authentication.md). Skip the saved plan for trivial tasks: typo fixes, one-line changes, simple config edits.

Match the plan's length to the task. A small feature needs a few paragraphs. A large refactor needs sections like objective, approach, files affected, and risks. Use judgment.

Describe outcomes, affected domains, database entities, fields, workflows, risks, and validation criteria. Leave out code snippets, class names, method names, migration names, file paths, and commands, unless I ask for them.

Read an existing plan before starting work on that task. If the approach changes during implementation, update the plan file to match what actually happened.

## Documentation

Only create documentation files when I ask for them. Plan files are the exception, they follow the Plan Mode rules above.

Never use dashes (— or -) as punctuation in documentation or a README. Use periods, commas, or parentheses instead.

## Coding Standards

For Laravel/PHP work, use the php-guidelines-from-7span and laravel-api-generator skills first. Laravel Boost's own skills (laravel-best-practices, testing-best-practices, and others) apply too. When they conflict with ours, check `.ai/rules` for the settled call before picking one.

## Git

Never push, pull, commit, merge, rebase, or create a branch without explicit approval. Read-only git commands (status, diff, log) are always fine.

## Typing Policy

Every new PHP file needs explicit types: method parameters, return types, and class properties. Add PHPDoc for complex types (arrays, generics, unions) that Larastan needs. Baseline is Larastan level 5.

`declare(strict_types=1)` is added automatically by Laravel Pint. Never add it by hand.

## Precedence

If these personal rules conflict with the Laravel Boost guidelines below, the personal rules win.

## Behavioral Rules

These rules apply to every task in this project unless explicitly overridden.
Bias: caution over speed on non-trivial work. Use judgment on trivial tasks.

## Rule 1 — Think Before Coding

State assumptions explicitly. If uncertain, ask rather than guess.
Present multiple interpretations when ambiguity exists.
Push back when a simpler approach exists.
Stop when confused. Name what's unclear.

## Rule 2 — Simplicity First

Minimum code that solves the problem. Nothing speculative.
No features beyond what was asked. No abstractions for single-use code.
Test: would a senior engineer say this is overcomplicated? If yes, simplify.

## Rule 3 — Surgical Changes

Touch only what you must. Clean up only your own mess.
Don't "improve" adjacent code, comments, or formatting.
Don't refactor what isn't broken. Match existing style.

## Rule 4 — Goal-Driven Execution

Define success criteria. Loop until verified.
Don't follow steps. Define success and iterate.
Strong success criteria let you loop independently.

## Rule 5 — Use the model only for judgment calls

Use LLM for: classification, drafting, summarization, extraction.
Do NOT use LLM for: routing, retries, deterministic transforms.
If code can answer, code answers.

## Rule 6 — Token budgets are not advisory

Per-task: 4,000 tokens. Per-session: 30,000 tokens.
If approaching budget, summarize and start fresh.
Surface the breach. Do not silently overrun.

## Rule 7 — Surface conflicts, don't average them

If two patterns contradict, pick one (more recent / more tested).
Explain why. Flag the other for cleanup.
Don't blend conflicting patterns.

## Rule 8 — Read before you write

Before adding code, read exports, immediate callers, shared utilities.
"Looks orthogonal" is dangerous. If unsure why code is structured a way, ask.

## Rule 9 — Tests verify intent, not just behavior

Tests must encode WHY behavior matters, not just WHAT it does.
A test that can't fail when business logic changes is wrong.

## Rule 10 — Checkpoint after every significant step

Summarize what was done, what's verified, what's left.
Don't continue from a state you can't describe back.
If you lose track, stop and restate.

## Rule 11 — Match the codebase's conventions, even if you disagree

Conformance > taste inside the codebase.
If you genuinely think a convention is harmful, surface it. Don't fork silently.

## Rule 12 — Fail loud

"Completed" is wrong if anything was skipped silently.
"Tests pass" is wrong if any were skipped.
Default to surfacing uncertainty, not hiding it.
