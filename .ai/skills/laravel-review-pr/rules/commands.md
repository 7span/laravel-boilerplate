#### Artisan Command Conventions

These reflect this project's actual, currently-used conventions in `app/Console/Commands/*.php` — not a generic style guide. Where the two disagreed, real usage wins.

Flag any of the following in a new or changed Artisan command class:

- `$signature` not kebab-case with a namespace prefix (e.g. `fasticket:make-season-published`, `media:delete-temp-files`, `system:hard-delete-data`) — every command in this project is namespaced this way; a bare unprefixed or non-kebab signature is inconsistent
- Feedback given via `$this->comment()` / `$this->info()` / `$this->output` / a progress bar instead of the `Log::` facade (`Log::info(...)`, `Log::warning(...)`, `Log::error(...)`) — none of this project's existing commands use console-output feedback; all logging goes through `Log::`, since these commands run on a schedule with no attached terminal
- A destructive command (bulk delete, bulk mutate) added with no `--dry-run` option (see `system:hard-delete-data` for the existing pattern) — this is a correctness/safety concern, not just a style preference, so flag it even though only one existing command has adopted it so far
- `handle()` with no `try`/`catch` wrapping the command's core logic — existing commands consistently wrap work in `try { ... } catch (Throwable $exception) { Log::error(...); throw new CustomException(...); }`; flag a new command that lets an exception propagate unlogged
