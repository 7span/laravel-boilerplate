# GitHub — Post

Posting findings as inline PR review comments. **Skip this entire file if `post_comments` is false.**

## Attribution

Comments are posted via plain `gh api` REST calls, not `git commit`. No co-author trailer is ever generated. The posted body must contain only the finding text — never add "Posted by Claude" or any signature. Every comment is attributed solely to the `$GITHUB_PAT` account.

---

### Step 8 — Post findings as inline PR comments

Post every finding from Step 7 (errors, warnings, and suggestions) as an inline review comment on its exact file and line, using the `commit_id` from Step 2.

**Always submit a real review — never use the single-comment endpoint.** `POST .../pulls/{pr}/comments` (one call per comment) silently creates an unsubmitted pending review that is invisible to the REST comments list and GraphQL `reviewThreads`, and can vanish entirely. Always batch all findings into one call to `POST .../pulls/{pr}/reviews` with `"event": "COMMENT"` — this submits immediately and leaves no pending state.

**Skip duplicates — but only unresolved ones.** Duplicate detection must use the GraphQL API (REST has no `isResolved`). A finding is "already posted" only if it matches a comment in a thread where `isResolved: false`. Comments in resolved threads do not block re-posting.

```bash
GH_TOKEN=$GITHUB_PAT gh api graphql -f query='
  query($owner: String!, $repo: String!, $pr: Int!) {
    repository(owner: $owner, name: $repo) {
      pullRequest(number: $pr) {
        reviewThreads(first: 100) {
          nodes {
            isResolved
            comments(first: 50) {
              nodes { path line body }
            }
          }
        }
      }
    }
  }' -F owner="{owner}" -F repo="{repo}" -F pr={pr_number} \
  --jq '[.data.repository.pullRequest.reviewThreads.nodes[] | select(.isResolved == false) | .comments.nodes[] | {path, line, body}]' \
  > /tmp/pr-{pr_number}-open-comments.json
```

If `reviewThreads` paginates beyond 100 threads, follow `pageInfo.hasNextPage`/`endCursor` — never truncate silently.

For each finding, compare against `/tmp/pr-{pr_number}-open-comments.json`: skip if an entry has the same `path`, `line`, and `body` (exact string match after trimming whitespace). Count as "already posted (unresolved)".

**Comment body format:**

```
{icon} **{Severity}**

{finding text, code symbols in backticks}
```

Build the comments array using `jq -n` (never manual string concatenation) so backticks, `$`, and quotes are escaped correctly:

```bash
jq -n '[
  {path: "{filename}", line: {n}, side: "RIGHT", body: "{icon} **{Severity}**\n\n{finding text}"},
  ...
]' > /tmp/pr-{pr_number}-review-comments.json
```

If this array is empty (all duplicates or zero findings), stop here. Print `No new findings to post; {S} already covered by open review comments.`

Otherwise submit in one call:

```bash
jq -n --arg commit_id "{commit_id}" --slurpfile comments /tmp/pr-{pr_number}-review-comments.json \
  '{commit_id: $commit_id, event: "COMMENT", body: "", comments: $comments[0]}' \
  > /tmp/pr-{pr_number}-review-payload.json

GH_TOKEN=$GITHUB_PAT gh api repos/{owner}/{repo}/pulls/{pr_number}/reviews \
  --input /tmp/pr-{pr_number}-review-payload.json
```

**Rules:**

- `event: "COMMENT"` submits immediately without approving or requesting changes.
- The top-level review `body` stays empty — per-comment bodies carry all content.
- On a `422`: GitHub's error message names the offending `path`/`line`. Drop that entry from the comments file, rebuild the payload, and resubmit — never drop the whole review silently.
- Print final tally: `Posted {P} new, skipped {S} already-open duplicates, {F} failed — of {X} errors, {Y} warnings, {Z} suggestions on PR #{pr_number}.`
