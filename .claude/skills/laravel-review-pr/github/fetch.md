# GitHub — Fetch

All GitHub data-fetching steps. Use `gh` CLI for every call.

**Auth:** two modes, resolved once in Step 1 and reused for every call below.
- **`pat` mode** — `$GITHUB_PAT` is set. Every `gh` call is prefixed with `GH_TOKEN=$GITHUB_PAT`. Required for `--comment` and `--history`.
- **`session` mode** — `$GITHUB_PAT` is unset and neither `--comment` nor `--history` was passed. Every `gh` call runs unprefixed, relying on whatever account is already authenticated via `gh auth login` on this machine. Steps 4 and 8 never run in this mode — both require `pat` mode, and Step 1 already stops before here if either flag was requested without a PAT.

---

### Step 1 — Resolve auth mode and repo identity

Check PAT is set using this exact command (keep it identical every run so it stays allowlisted):

```bash
echo "PAT set: $([ -n "$GITHUB_PAT" ] && echo yes || echo no)"
```

**If PAT is set** — `auth_mode = pat`. Skip ahead to resolving the repo below.

**If PAT is not set:**

- If `post_comments` or `update_history` is true, stop immediately — do not fall back to a `gh` session for either:

  > `--comment` and `--history` require `$GITHUB_PAT`. Add it to `.claude/settings.local.json` under `"env": { "GITHUB_PAT": "ghp_..." }`, or rerun without `--comment`/`--history` to use your local `gh` session instead.

- Otherwise (neither flag was requested), check for an authenticated `gh` session using this exact command (keep it identical every run so it stays allowlisted):

  ```bash
  gh auth status &>/dev/null && echo "gh session: yes" || echo "gh session: no"
  ```

  - If authenticated — `auth_mode = session`. Continue to resolving the repo below.
  - If not authenticated, stop immediately:

    > Neither `$GITHUB_PAT` nor an authenticated `gh` session is available. Either add `$GITHUB_PAT` to `.claude/settings.local.json` under `"env"`, or run `gh auth login`.

Resolve repo owner and name (prefix with `GH_TOKEN=$GITHUB_PAT` in `pat` mode, omit the prefix in `session` mode):

```bash
gh repo view --json owner,name -q '.owner.login + "/" + .name'
```

Returns `Owner/Repo` (e.g. `NextWaveSA/FasTicket2.0-Backend`).

---

### Steps 2–4 — Fetch PR metadata, diffs, and reviewer history in parallel

Steps 2, 3, and 4 only depend on `{owner}/{repo}` from Step 1. Issue whichever of these are needed as parallel tool calls in a single batch. Step 3 requires a `pr_number` and is skipped entirely in history-only mode (no PR number given); Step 2 runs only if `post_comments` is true (which itself requires a `pr_number`); Step 4 runs only if `update_history` is true, and is the one step that runs with no `pr_number` at all. Use the `GH_TOKEN=$GITHUB_PAT`-prefixed form in `pat` mode, the unprefixed form in `session` mode.

**Step 2 — PR metadata**

**Skip entirely if `post_comments` is false.** Nothing in the base review (Steps 5–7) reads PR title, body, or author — only `commit_id`, needed for Step 8's inline comments. Since `--comment` already forces `pat` mode back in Step 1, this step only ever runs in `pat` mode:

```bash
GH_TOKEN=$GITHUB_PAT gh api repos/{owner}/{repo}/pulls/{pr_number} \
  --jq '{commit_id: .head.sha}'
```

Capture `commit_id` (head SHA) — required in Step 8 to post inline comments.

**Step 3 — Changed files with diffs**

**Skip entirely if no `pr_number` was given (history-only mode).** There is no PR to diff.

Prefix with `GH_TOKEN=$GITHUB_PAT` in `pat` mode, omit the prefix in `session` mode:

```bash
gh api "repos/{owner}/{repo}/pulls/{pr_number}/files?per_page=100" \
  --paginate \
  --jq '[.[] | {filename, status, patch}]' > /tmp/pr-{pr_number}-files.json

jq 'length' /tmp/pr-{pr_number}-files.json
```

Print the `jq 'length'` count as the total file count. Skip files where `patch` is null or absent (binary files, pure renames). Process every file — do not stop early. If the combined diff is too large, process in batches of 10.

**Step 4 — Reviewer's past comment patterns**

Always `pat` mode by this point — `update_history` being true already forced `pat` mode back in Step 1, so no unprefixed variant exists for this step.

**Skip entirely if `update_history` is false.** When skipped, Step 6 still applies patterns already persisted in `rules/learned-patterns.md`.

When `update_history` is true — fetch up to 200 recent PR review comments filtered to 7Span team (`login` contains `7span`, case-insensitive):

```bash
GH_TOKEN=$GITHUB_PAT gh api "repos/{owner}/{repo}/pulls/comments?sort=created&direction=desc&per_page=100" \
  --paginate \
  --jq '.[] | {login: .user.login, body}' \
  | jq -s '[.[] | select(.login | ascii_downcase | contains("7span"))] | .[0:200]' \
  > /tmp/pr-reviewer-comments.json
```

If no comments match, print: "No past reviewer comment patterns found (no 7span team comments in recent history)." and skip Step 4a.

**Step 4a — Persist meaningful patterns into rules files**

Analyze comments for recurring themes. A pattern is "meaningful" if it:
- Appears in 3 or more distinct comments (not just repeated on the same PR), AND
- Is not already covered by any file under `rules/`

Read every existing file in `rules/` before deciding what is new.

Append new patterns to `rules/learned-patterns.md`:

```markdown
- {one-sentence rule, code symbols in backticks}
```

If a pattern is already in `learned-patterns.md` but wording can be improved, update in place — never duplicate. Never add a pattern already covered (even in different wording) by any other rules file.

Print a summary: `{N} new patterns added, {U} updated, {S} skipped (already covered).`
