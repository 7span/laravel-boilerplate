---
name: laravel-review-pr
description: Reviews a GitHub PR by number. By default prints findings to the terminal only. Pass --comment to also post each finding as an inline PR review comment on GitHub (requires $GITHUB_PAT). Pass --history alone (no PR number) to refresh rules/learned-patterns.md from recent reviewer comments without reviewing a PR. Triggered by /laravel-review-pr [pr-number] [--comment] [--history].
---

# PR Review

Review a GitHub pull request against this project's Laravel/PHP and Scramble API documentation standards. Print findings, then optionally post them as inline review comments.

## Activation

Triggered by `/laravel-review-pr [pr-number] [--comment] [--history]`.

Parse the skill args before doing anything else:
- Extract the PR number (first numeric token), if present. It is optional only in the history-only case below.
- Check whether `--comment` appears in the args. Store as boolean `post_comments`.
- Check whether `--history` appears in the args. Store as boolean `update_history`.

Determine the run mode:
- **History-only** — `update_history` is true, `post_comments` is false, and no PR number was given. This is the one case where a PR number isn't required, because `--history` only fetches repo-wide reviewer comments and updates `rules/learned-patterns.md` (Steps 4 and 4a) — it never reads a specific PR's diff. Skip Steps 2, 3, 5, 6, 7, and 8 entirely; run only Step 1 and Steps 4–4a, then stop.
- **Full review** — a PR number was given (with or without `--comment`/`--history`). Run all steps as described below.
- **Missing PR number** — no PR number was given, and either `post_comments` is true or `update_history` is false. Stop immediately and ask the user for a PR number: `--comment` posts to a specific PR, and a plain review has nothing to diff without one.

`$GITHUB_PAT` is required for `--comment` and `--history` (including history-only mode), optional otherwise:
- If `--comment` or `--history` was passed and `$GITHUB_PAT` is not set, the skill stops immediately in Step 1 — it never falls back to an ambient `gh` session for either of these.
- If neither flag was passed and `$GITHUB_PAT` is not set, the base review (fetch PR, parse diff, apply rules, print findings) still runs, using whatever GitHub account is already authenticated via `gh auth login` on this machine. If that session doesn't exist either, the skill stops with instructions for both options.

See `github/fetch.md` Step 1 for the exact resolution logic.

This project uses one shared `$GITHUB_PAT` for the whole team, so posted comments always show that same GitHub account regardless of who ran the review — GitHub's API has no author-override field.

## Execution Steps

Work through these steps sequentially. Stop and surface any error rather than guessing.

**History-only mode:** run Step 1 and Steps 4–4a from `github/fetch.md` only, then stop — do not proceed to Steps 2, 3, or 5–8. There is no PR review to print or post in this mode; the fetch.md Step 4a summary line is the entire output.

### Steps 1–4 — Fetch from GitHub

Read `.claude/skills/laravel-review-pr/github/fetch.md` and follow it exactly.

### Step 5 — Parse added lines per file

For each file's `patch` string:

1. Split on newlines.
2. Each hunk header matches `@@ -old_start[,old_count] +new_start[,new_count] @@`. Set `line = new_start` at each hunk.
3. For each line:
   - Starts with `+` (not `+++`): **added line** — record `(line, content)`, increment `line`.
   - Starts with `-` (not `---`): deleted — skip, do not increment `line`.
   - Starts with ` `: context — skip, increment `line`.
4. Collect `(line_number, content)` pairs per file. These are the only lines to review.

### Step 6 — Review added lines

Rules live in `.claude/skills/laravel-review-pr/rules/`. Load only what's relevant to the files touched in this diff (from Step 3) — skip the rest.

**Always load** (apply to any PHP diff): `loops-and-queries.md`, `php-correctness.md`, `laravel-conventions.md`, `naming.md`, `dependency-injection.md`, `enums.md`, `learned-patterns.md`.

**Load conditionally**, based on the touched filenames:
- `migrations.md` — any file under `database/migrations/`
- `commands.md` — any file under `app/Console/Commands/`
- `routes.md` — any file under `routes/`
- `form-requests.md` — any file matching `*Request.php`
- `authorization.md` — any file under `app/Http/Controllers/`
- `resources.md` and `scramble.md` — any file under `app/Http/Resources/`, `app/Models/`, or `app/Http/Controllers/`
- `translations.md` — only if an added line contains `__(`
- `notifications.md` — only if an added line references `Mail::`, `Mailable`, or `Notification`

If a file's relevance is ambiguous, load it anyway — this is about skipping categories with no plausible match, not narrowing coverage.

Apply all loaded rules to the added lines only.

**Non-negotiable on every review:** explicitly check every added line for N+1 queries and duplicate queries per `rules/loops-and-queries.md`. Never skip this check and never downgrade these findings below 🔴 Error, regardless of how minor the change looks.

Verifying a finding often requires checking the wider codebase — e.g. confirming a relation, model, or sibling file actually exists before flagging something. Use the `Grep` and `Glob` tools for this, never raw `find`/`ls`/`grep` through Bash. `Grep`/`Glob` aren't Bash calls, so they never hit a permission prompt; shelling out through Bash for the same lookup does, since those commands aren't on the project's allowlist and adding every possible read-only shell command there isn't worth the growing surface area.

When writing a finding's one-sentence text, wrap every code symbol in backticks — variable names (`` `$campaign` ``), method/property access (`` `->update()` ``), class/enum references (`` `CampaignStatus::Failed` ``), function calls (`` `in_array()` ``), and file/route names. Plain-English parts stay unformatted.

Patterns learned from previous `--history` runs are persisted in `rules/learned-patterns.md` — always loaded above; apply them with equal weight to every other rule.

### Step 7 — Print findings

Severity legend:
- 🔴 **Error** — causes a bug, type failure, security hole, or runtime break
- 🟡 **Warning** — violates a convention with a real consequence (missing FormRequest, `env()` in code, missing `exists:` on `_id`, direct `Mail::send()`). N+1 queries, duplicate queries, and nested loops with a query inside are always 🔴 Error, never Warning — see `rules/loops-and-queries.md`.
- 🔵 **Suggestion** — readability or style improvement; low risk if left

**If `post_comments` is false**, print one Markdown table for all findings across every file. Columns: Type, File, Line, Comment. Format:

```
| Type | File | Line | Comment |
|------|------|------|---------|
| 🔴 Error | `{filename}` | {n} | {One sentence: what rule is violated and why it matters, with code symbols in backticks.} |
| 🟡 Warning | `{filename}` | {n} | {One sentence.} |
| 🔵 Suggestion | `{filename}` | {n} | {One sentence.} |
```

Sort rows by severity first (all Errors, then all Warnings, then all Suggestions), and by file then line number within each severity group — this surfaces the most critical issues first. Omit rows for files with no findings. If there are zero findings across all files, skip the table and print `✅ No findings — this PR looks clean!` instead. End with:

```
---
{X} errors, {Y} warnings, {Z} suggestions across {N} files reviewed.
```

**If `post_comments` is true**, skip the table entirely — findings still get posted individually in Step 8, so the full table would just duplicate what's about to appear on GitHub. Print only the one-line summary: `{X} errors, {Y} warnings, {Z} suggestions across {N} files reviewed.`, or `✅ No findings — this PR looks clean!` if there are zero findings.

Never suggest git commands. If `post_comments` is true, proceed to Step 8. Otherwise stop here.

### Step 8 — Post to GitHub

Read `.claude/skills/laravel-review-pr/github/post.md` and follow it exactly.
