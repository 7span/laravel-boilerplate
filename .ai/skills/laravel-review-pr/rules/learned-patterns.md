#### Learned Patterns

Patterns auto-written by `/laravel-review-pr --history` from recurring 7Span reviewer comments.
Each line is a rule in the same format as other rules files.
Do not edit manually — run `/laravel-review-pr <pr> --history` to update.

- Any new controller or service method, or FormRequest validating input, that reads, updates, or deletes an organizer-scoped resource (`Event`, `Season`, `Campaign`, `Order`, etc.) must verify the resource belongs to the currently authenticated organizer; flag methods or FormRequest `rules()`/`prepareForValidation()` missing this organizer-ownership check even when a different `$this->authorize()` call already exists for another ability.
- A new route that should be scoped to the current organizer belongs nested inside the existing `AddOrganizerFilter` middleware group; flag a route added as a flat top-level entry (or with manual organizer filtering bolted on) when it could instead live inside that group.
