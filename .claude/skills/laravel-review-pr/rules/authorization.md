#### Authorization Checks

**Missing `$this->authorize()` call in a protected controller action.**
Flag any controller method that reads, creates, updates, or deletes a model tied to a specific user/tenant (i.e. not a fully public endpoint) but does not call `$this->authorize('gate-name')` or `$this->authorize($ability, $model)` at the top of the method. Authorization belongs in the controller, never in the FormRequest.

Do not flag intentionally public endpoints (login, registration, public listings with no per-record ownership). Only flag when either:
- A policy/gate for the same model already exists elsewhere in the codebase and isn't being called for this new action, or
- The action clearly implies per-record ownership (e.g. a `show`/`update`/`destroy` on a user-owned resource like `$order->user_id`) but has no authorization guard at all.

**Authorization logic inside FormRequest `authorize()`.**
See `form-requests.md` — the other half of the same mistake: any check beyond `return true;` must move to the controller.
