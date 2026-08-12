<!-- PROJECT BANNER -->
<p align="center">
  <img src="public/assets/img/logo.png" alt="Laravel Boilerplate Logo" width="120"/>
</p>

<h1 align="center">Laravel Boilerplate</h1>

<p align="center">
  <b>A robust starter project using <code>Laravel 13</code> for rapid, modern API development.</b><br>
  <i>Clean structure, best practices, token authentication, and a suite of developer tools out of the box.</i>
</p>

<p align="center">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-13.x-red?logo=laravel&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.3%2B-777bb4?logo=php&logoColor=white">
  <img alt="License" src="https://img.shields.io/badge/license-MIT-blue.svg">
  <img alt="Code Style" src="https://img.shields.io/badge/code%20style-pint-ff69b4">
</p>

---

## 🚀 Quick Start

```bash
# 1. Clone the repository
git clone <your-repo-url>
cd laravel-boilerplate

# 2. One-shot setup (install, .env, key, migrate, assets)
composer setup

# 3. Configure Git hooks (Husky)
git config core.hooksPath .husky

# 4. Start everything (server + queue + logs + vite)
composer dev
```

<details>
<summary>Manual setup (instead of <code>composer setup</code>)</summary>

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

</details>

> **Requirements:** PHP `^8.3`, Composer, Node.js. `DB_CONNECTION` defaults to `sqlite`; switch to MySQL/PostgreSQL in `.env` if needed. Redis (`predis`) is used for Horizon queues.

---

## ✨ Features & Packages

-   **[Authentication (Laravel Sanctum)](https://laravel.com/docs/13.x/sanctum)** — token-based API auth
-   **[Role & Permission Management (Spatie Laravel Permission)](https://spatie.be/docs/laravel-permission/v6/introduction)**
-   **[Query Filtering & Sorting (Spatie Query Builder)](https://spatie.be/docs/laravel-query-builder)**
-   **[Media/File Management (Plank Mediable)](https://github.com/plank/laravel-mediable)** + S3 via [Flysystem](https://github.com/thephpleague/flysystem-aws-s3-v3)
-   **[API Documentation (Scramble)](https://scramble.dedoc.co/)** — generated from types, no annotations required
-   **[Request Monitoring (Laravel Telescope)](https://laravel.com/docs/13.x/telescope)**
-   **[Log Management (Log Viewer)](https://github.com/opcodesio/log-viewer)**
-   **[Queue Monitoring (Laravel Horizon)](https://laravel.com/docs/13.x/horizon)**
-   **[Push Notifications (OneSignal channel)](https://github.com/laravel-notification-channels/onesignal)**
-   **[Code Style (Laravel Pint)](https://laravel.com/docs/13.x/pint)**
-   **[Static Analysis (Larastan/PHPStan)](https://github.com/larastan/larastan)** — level 5 + strict & banned-code rules
-   **[AI-assisted development (Laravel Boost)](https://laravel.com/docs/13.x/boost)** — plus `.ai/` guidelines & skills

---

## ⚙️ Custom Environment Variables

> In addition to standard Laravel variables, set these in your `.env`:

-   `FRONT_WEBSITE_URL` — The URL of your frontend application
-   `MASTER_PASSWORD` — Master password for privileged/admin operations
-   `MASTER_OTP` — Master OTP code for bypassing OTP verification
-   `SOFT_DELETE_RETENTION_DAYS` — Days to retain soft-deleted records (default `90`)
-   `CDN_ENABLE` — Enable/disable CDN usage for media URLs
-   `CDN_URL` — The base URL of your CDN for media assets
-   `TEMP_FILE_DELETE_AFTER_DAYS` — Days before unlinked/temp uploads are purged (default `2`)
-   `DEVELOPER_USERNAME` / `DEVELOPER_PASSWORD` — Credentials for the developer panel
-   `TELESCOPE_PATH` / `HORIZON_PATH` — Override developer tool paths (default `developer/telescope`, `developer/horizon`)

---

## 🗂️ Custom Configuration Files

-   `site.php` — Site-wide settings (frontend URL, pagination limit, master password, roles, OTP, soft-delete retention)
-   `media.php` — Media/file upload settings (tags, directories, CDN, aggregate types, MIME mappings)
-   `developer.php` — Developer panel credentials, session key, and auth redirect route
-   `scramble.php` — API documentation generation settings

---

## 🌍 Localization

Localization files live in `lang/en/`:

-   `email.php` — Email-related strings
-   `entity.php` — Entity names/messages
-   `message.php` — General messages

Each file returns an array of key-value pairs for use with Laravel's `__()` and `trans()` helpers. Models can use the `HasTranslations` trait for per-locale attributes.

---

## 📦 API Overview

All API routes are versioned and registered in `routes/api-v1.php` under the `api/v1` prefix (see `bootstrap/app.php`).

### Supported Endpoints

-   **Auth:** Register, Login, Logout, Get Profile, Forget Password (OTP), Reset Password
-   **User:** Update Profile, Change Password, Change Status (Admin)
-   **Country:** List countries (with filters)
-   **Language:** List languages
-   **Master Settings:** List and detail endpoints
-   **Signed URL:** Generate signed URLs for file uploads

### API Documentation

Scramble generates the OpenAPI spec from your controllers, requests, and resources — no annotations needed. It is exposed behind the developer panel (configured in `app/Providers/AppServiceProvider.php`):

-   `/developer/docs/api` — Interactive UI
-   `/developer/docs/api.json` — OpenAPI document

Bearer-token security is applied to the whole document, and `#[Group]` / `#[SchemaName]` attributes are used to organise operations and schemas.

### Folder Structure

-   `app/Http/Controllers/Api/V1/` — API controllers (RESTful, thin, service-driven)
-   `app/Http/Requests/` — FormRequest validation classes (grouped by domain)
-   `app/Http/Resources/` — API resource & collection transformers
-   `app/Services/` — Business logic (`AuthService`, `UserService`)
-   `app/Models/` — Eloquent models
-   `app/Enums/` — Enums for statuses and typed constants (`UserStatus`)
-   `app/Traits/` — Reusable model/controller traits
-   `app/Libraries/` — Helper classes (`Helper`)
-   `app/Notifications/` — Notification classes (`WelcomeUser`)
-   `app/Exceptions/` — `CustomException` for consistent API errors

### Reusable Traits

-   `ApiResponser` — Standardised success/error JSON responses
-   `BaseModel` — Shared model conventions
-   `ResourceFilterable` — Filters, sorts, includes & appends via Spatie Query Builder
-   `HasTranslations` — Locale-aware attribute accessors
-   `HasUserActions` — Auto-fills created-by / updated-by user IDs

### Exception Handling

`bootstrap/app.php` converts API `404`s into localized `CustomException` messages — both missing models (`... data not found`) and unknown routes (`route ... not found`) — so clients always get a consistent JSON error shape.

---

## 🛠️ Custom Functionality

### Media Handling

-   Uploads are managed through Mediable with tags/directories declared in `config/media.php`
-   Optional CDN rewriting of media URLs (`CDN_ENABLE`, `CDN_URL`)
-   Unattached/temp files are cleaned up after `TEMP_FILE_DELETE_AFTER_DAYS`
-   `App\Http\Resources\Media\Resource` exposes a consistent media payload

### Mail Layout Customization

-   All emails use a custom Blade layout: `resources/views/emails/layouts/master.blade.php`
    -   Branded header (`emails/includes/header.blade.php`) with logo
    -   Localized greetings and sign-off
    -   Centralized content section (`@yield('content')`)
    -   Footer with copyright

### Notifications

-   Notifications are plain Laravel notification classes in `app/Notifications/` (e.g. `WelcomeUser`), delivered over mail/database channels
-   OneSignal channel is installed for push notifications
-   Add new types by creating additional classes in `app/Notifications/`

---

## 🧑‍💻 Developer Tools

### Developer Panel

All tooling sits behind the `developer` prefix, guarded by the `DeveloperAuth` middleware (session-based):

| Path                        | Tool                     |
| --------------------------- | ------------------------ |
| `/developer/login`          | Login for developer area |
| `/developer/dashboard`      | Developer dashboard      |
| `/developer/telescope`      | Laravel Telescope        |
| `/developer/horizon`        | Laravel Horizon          |
| `/developer/log-viewer`     | Log Viewer               |
| `/developer/docs/api`       | API documentation UI     |

-   **Authentication:** `DEVELOPER_USERNAME` and `DEVELOPER_PASSWORD` in `.env` (see `config/developer.php`)

### Code Quality

```bash
./vendor/bin/pint                                   # auto-format (Laravel preset + custom rules)
./vendor/bin/phpstan --memory-limit=2G analyse      # static analysis (level 5)
composer test                                       # clear config + run the test suite
```

A Husky `pre-commit` hook runs Pint, re-stages the fixes, then blocks the commit if PHPStan fails.

> If you have issues committing, ensure the hook is executable:
>
> ```bash
> chmod ug+x .husky/pre-commit
> ```

VS Code / Cursor users can bind Pint to `Ctrl+S` for instant formatting.

### AI Assistance

-   `.ai/guidelines/basic-guidelines.md` — project coding guidelines for AI agents
-   `.ai/skills/laravel-api-generator` — scaffold API modules following this boilerplate's conventions
-   `.ai/skills/php-guidelines-from-7span` — 7Span PHP standards
-   Laravel Boost is installed (`composer require laravel/boost --dev` already done) and wired up for Claude Code & Cursor

---

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change. Make sure Pint, PHPStan, and the test suite pass before pushing.

---

## 📄 License

[MIT](https://opensource.org/licenses/MIT)

---

## 💬 Support

For questions, suggestions, or support, please open an issue or contact the maintainer.
