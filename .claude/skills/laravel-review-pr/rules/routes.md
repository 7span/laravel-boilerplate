#### Route Conventions

These reflect this project's actual, currently-used conventions in `routes/*.php` (verified against `api-v1.php`, `admin-v1.php`, `organizer-v1.php`, `usher-v1.php`, `web.php`) — not a generic style guide. Where the two disagreed, real usage wins.

Flag any of the following in changed route files:

- URL segment not kebab-case or not a plural resource noun (e.g. `/errorOccurrence` or `/error_occurrences` instead of `/error-occurrences`)
- Route name segment not kebab-case (e.g. `->name('attendees.pending_invitations')`, `->name('order_tickets.gift')`) — this project's route names mirror the kebab-case URL (`gift-requests.index`, `tickets.price-caps`, `season-events.details`); snake_case name segments are the inconsistent minority and should be flagged for cleanup, not treated as correct
- Multi-word route parameter not camelCase (e.g. `{order_ticket}` instead of `{orderTicket}`) — this project consistently uses camelCase for bound route parameters (`{giftRequest}`, `{orderTicket}`, `{paymentGateway}`, `{refundPolicy}`, `{refundRequest}`), matching the camelCase variable name used in the corresponding controller method signature for route-model binding
- Old-style `'Controller@method'` string action instead of `[Controller::class, 'method']` tuple notation, `Route::controller(X::class)->group()` shorthand, or a direct `Controller::class` reference for a single-action invokable controller — all three of these are already established, legitimate patterns in this codebase; only flag the old string-based syntax or a raw Closure handling non-trivial logic
- `Route::resource(...)` used for an API-only controller instead of `Route::apiResource(...)` (`apiResource` omits `create`/`edit`, which API-only controllers don't need)
- HTTP verb embedded in the URL (`/getOrders`, `/create-order`, `/orders/delete`) — the HTTP verb should carry the action instead
- A non-CRUD action added as a new top-level route when that same parent resource already has an established nested prefix elsewhere in the file (e.g. adding a new top-level `/refund-something` route when `orders/{order}/refund-*` already exists) — do not flag genuinely standalone utility routes with no natural parent resource (e.g. `check-username`, `generate-signed-url`), since those are an accepted pattern here
