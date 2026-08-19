<!-- PROJECT BANNER -->
<p align="center">
  <img src="public/assets/img/logo.png" alt="Laravel Boilerplate Logo" width="120"/>
</p>

<h1 align="center">Laravel Boilerplate</h1>

<p align="center">
  <b>A robust starter project using <code>Laravel 13</code> for rapid, modern API development.</b><br>
  <i>Clean structure, best practices, authentication, and a suite of developer tools out of the box.</i>
</p>

<p align="center">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-13.x-red?logo=laravel&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.3+-777bb4?logo=php&logoColor=white">
  <img alt="License" src="https://img.shields.io/badge/license-MIT-blue.svg">
  <img alt="Code Style" src="https://img.shields.io/badge/code%20style-pint-ff69b4">
</p>

---

## 🚀 Quick Start

```bash
# 1. Clone the repository
$ git clone <your-repo-url>
$ cd laravel-boilerplate

# 2. Install dependencies
$ composer install
$ npm install && npm run build

# 3. Copy .env and configure
$ cp .env.example .env

# 4. Configure Git hooks (Husky)
$ git config core.hooksPath .husky

# 5. Generate app key and Passport signing keys
$ php artisan key:generate
$ php artisan passport:keys

# 6. Run migrations and seeders
$ php artisan migrate --seed

# 7. Start the server
$ php artisan serve
```

`composer setup` runs steps 2, 3, 5 and 6 in one go, and `composer dev` boots the server,
queue worker, log tail and Vite together.

---

## ✨ Features & Packages

-   **[Authentication (Laravel Passport)](https://laravel.com/docs/13.x/passport)**
-   **[Role & Permission Management (Spatie Laravel Permission)](https://spatie.be/docs/laravel-permission/v6/introduction)**
-   **[Media/File Management (Plank Mediable)](https://github.com/plank/laravel-mediable)**
-   **[Query String Filtering & Sorting (Spatie Query Builder)](https://spatie.be/docs/laravel-query-builder)**
-   **[API Documentation (Scramble)](https://scramble.dedoc.co)**
-   **[Push Notifications (OneSignal channel)](https://github.com/laravel-notification-channels/onesignal)**
-   **[Request Monitoring (Laravel Telescope)](https://laravel.com/docs/13.x/telescope)**
-   **[Log Management (Log Viewer)](https://github.com/opcodesio/log-viewer)**
-   **[Queue Monitoring (Laravel Horizon)](https://laravel.com/docs/13.x/horizon)**
-   **[Code Style (Laravel Pint)](https://laravel.com/docs/13.x/pint)**
-   **[Static Analysis (Larastan/PHPStan)](https://github.com/larastan/larastan)**
-   **Developer panel protection** — first-party `DeveloperAuth` middleware, configured in `config/developer.php`

---

## ⚙️ Custom Environment Variables

> In addition to the standard Laravel variables, set these in your `.env`:

-   `FRONT_WEBSITE_URL` — The URL of your frontend application, used in mails and reset links
-   `MASTER_PASSWORD` — Master password for privileged/admin operations
-   `MASTER_OTP` — Master OTP code for bypassing OTP verification
-   `DEVELOPER_USERNAME` / `DEVELOPER_PASSWORD` — Credentials for the developer panel
-   `LOG_DAILY_DAYS` — Days to retain log files, defaults to 30
-   `TELESCOPE_ENABLED` / `TELESCOPE_PATH` — Toggle Telescope and where it is served
-   `HORIZON_PATH` — Where the Horizon dashboard is served
-   `LOG_VIEWER_ENABLED` — Toggle the Log Viewer
-   `API_VERSION` — Version reported by the generated OpenAPI documents
-   `CDN_ENABLE` / `CDN_URL` — Toggle and base URL of your CDN for media assets
-   `AWS_URL` — Public base URL of the S3 bucket, set it when files are served through a CDN
-   `TEMP_FILE_DELETE_AFTER_DAYS` — Age at which unclaimed uploads are pruned
-   `SOFT_DELETE_RETENTION_DAYS` — Age at which soft-deleted rows are hard deleted
-   `NOTIFICATION_ENABLED` — Enable or disable the notification system (true/false)
-   `ONESIGNAL_APP_ID` / `ONESIGNAL_API_KEY` — OneSignal credentials for the **user** app
-   `ONESIGNAL_ADMIN_APP_ID` / `ONESIGNAL_ADMIN_API_KEY` — OneSignal credentials for the **admin** app
-   `PASSPORT_PRIVATE_KEY` / `PASSPORT_PUBLIC_KEY` — Only when the token signing keys come from the environment instead of `storage/oauth-*.key`

> `QUEUE_CONNECTION` and `CACHE_STORE` ship as `redis` because Horizon only supervises the
> redis queue driver. Switching them to `database` disables the Horizon dashboard.

---

## 🗂️ Custom Configuration File Structure

-   `site.php` — Site-wide settings (frontend URL, pagination, roles, OTP, master password, updatable setting keys)
-   `media.php` — Media/file upload settings (tags, directories, CDN, aggregate types, MIME mappings)
-   `developer.php` — Developer panel credentials, session key and redirect route
-   `language.php` — Locales exposed by the `languages` endpoints

---

## 🌍 Localization File Structure

Localization files are in `lang/<locale>/`:

-   `message.php` — General API messages, plus the nested `entity.*` lines (`:entity not found.` etc.)
-   `email.php` — Email subjects and body strings
-   `enum.php` — Human readable labels for the enums in `app/Enums/`
-   `auth.php`, `validation.php` — Framework strings, published so they can be customised

Each file returns an array of key-value pairs for use with Laravel's `__()` and `trans()` functions.
`lang/ar/email.php` is included as a reference translation, and the locale is chosen per request
from the `locale` header, falling back to the authenticated user's `locale` column
(see `app/Http/Middleware/SetLocale.php`).

---

## 📦 API Overview

All routes are versioned. `routes/api-v1.php` is served under `api/v1`, and
`routes/admin-v1.php` under `api/v1/admin`.

### Supported Endpoints

| Method | Endpoint | Description |
| --- | --- | --- |
| `POST` | `api/v1/register` | Register a user |
| `POST` | `api/v1/login` | Issue an access token |
| `POST` | `api/v1/forgot-password` | Mail a forgot-password OTP |
| `POST` | `api/v1/forgot-password/verify-otp` | Verify the OTP and receive a reset token |
| `POST` | `api/v1/reset-password` | Reset the password with that token |
| `POST` | `api/v1/logout` | Revoke the current token |
| `GET` | `api/v1/me` | Authenticated user profile |
| `POST` | `api/v1/me` | Update profile |
| `POST` | `api/v1/change-password` | Change password |
| `POST` | `api/v1/locale` | Update the user's locale |
| `GET` | `api/v1/countries` | List countries (filter, sort, paginate) |
| `GET` | `api/v1/languages`, `api/v1/languages/{language}` | List locales, fetch one translation file |
| `GET` | `api/v1/notifications` | List notifications |
| `GET` | `api/v1/notifications/unread-count` | Unread counter |
| `POST` | `api/v1/notifications/read`, `api/v1/notifications/unread` | Mark all, or given ids, as read/unread |
| `POST` | `api/v1/notifications/onesignal` | Register a device for push |
| `POST` | `api/v1/generate-signed-url` | Pre-signed S3 upload URL |
| `DELETE` | `api/v1/media/{media}` | Detach and delete a media record |
| `GET` | `api/v1/admin/settings` | List settings |
| `PUT` | `api/v1/admin/settings` | Update the keys listed in `site.setting_keys` |
| `POST` | `api/v1/admin/users/{user}/change-status` | Activate/deactivate a user |

> Documentation is auto-generated by Scramble: the user API at `/developer/docs/api`
> and the admin API at `/developer/docs/admin` (JSON at the same paths with a `.json` suffix).

### API Folder Structure

-   `app/Http/Controllers/Api/` — API controllers (RESTful, thin, service-driven)
-   `app/Http/Requests/` — FormRequest classes for validation
-   `app/Http/Resources/` — API resource and collection transformers
-   `app/Services/` — Business logic and service classes
-   `app/Models/` — Eloquent models
-   `app/Enums/` — Backed enums for statuses and types, with translated labels
-   `app/Rules/` — Custom validation rules
-   `app/Libraries/` — Helper libraries
-   `app/Traits/` — Shared model, response and filtering behaviour
-   `app/Channels/` — Custom notification channels
-   `app/Support/Scramble/` — Documentation extractors

### API Documentation with Minimal Code in Controllers

Scramble infers request bodies, responses and query parameters from FormRequest rules,
API Resources and route signatures, so controllers stay free of annotations. Two documents are
registered in `app/Providers/AppServiceProvider.php`, one per route prefix. Attributes such as
`#[Group]` and `#[SchemaName]` refine the output where the inference needs a hint, and
`app/Support/Scramble/GetQBParameterExtractor.php` documents the Spatie Query Builder
filter/sort/include parameters automatically.

---

## 🛠️ Custom Functionality

### Custom Artisan Commands

-   `php artisan media:delete-temp-files` — Deletes unclaimed uploads older than `TEMP_FILE_DELETE_AFTER_DAYS` and their `temp_files` rows (scheduled daily)
-   `php artisan system:hard-delete-data` — Permanently removes rows soft-deleted more than `SOFT_DELETE_RETENTION_DAYS` ago (opt-in, commented out in `routes/console.php`)
-   `php artisan telescope:prune --hours=24` — Prunes Telescope entries (scheduled daily)

### Custom Validation Rules & Libraries

-   **MediaRule:** Reusable validation for media/image fields (tags, mime types, nullable/required)
-   **MediaHelper:** File naming, extension detection, media attachment/deletion, aggregate type detection
-   **Helper:** OTP generation
-   **Image Optimization:** Configured via `config/mediable.php` for automatic optimization (JPEG, PNG, GIF, WebP, AVIF)

### Uploads

Clients ask for a pre-signed URL (`POST api/v1/generate-signed-url`), `PUT` the file straight to S3,
then send the returned key back with the owning resource. Each issued URL records a `temp_files`
row, so anything never claimed is pruned by `media:delete-temp-files`.

### Mail Layout Customization

-   All emails use a custom Blade layout: `resources/views/emails/layouts/master.blade.php`
    -   Branded header with logo
    -   Localized greetings and sign-off
    -   Centralized content section (`@yield('content')`)
    -   Footer with copyright

### Notification System

-   This boilerplate includes a robust notification system using Laravel's native features.

    -   **Channels supported:** `database` and `onesignal`, both swapped for the app's own
        implementations in `app/Channels/` so notifications write the extra `notifications` columns
        (`user_id`, `sent_by`, `title`, `description`, `type`) and push to the right OneSignal app.
    -   **How it works:** Notifications are classes in `app/Notifications/`. Add new types by adding
        classes there; `NOTIFICATION_ENABLED` is the master switch.
    -   **API integration:** Endpoints are available for listing, unread counts, marking read/unread,
        and registering devices. Requests through the `notification-read` middleware
        (`app/Http/Middleware/MarkNotificationsAsRead.php`) mark the listed notifications as read.

> See the `app/Notifications/` directory and related controllers/services for implementation details.

---

## 🧑‍💻 Developer Tools

### Developer Panel

-   `/developer/login` — Login for the developer tools
-   `/developer/dashboard` — Index of everything below
-   `/developer/telescope` — Laravel Telescope
-   `/developer/log-viewer` — Log Viewer
-   `/developer/horizon` — Laravel Horizon
-   `/developer/docs/api`, `/developer/docs/admin` — Scramble API documentation
-   **Authentication:** Protected by `DEVELOPER_USERNAME` and `DEVELOPER_PASSWORD` in `.env`,
    enforced by the `developer` middleware alias

### Pre-commit Checklist & Code Quality

-   Code style check: `./vendor/bin/pint`
-   Static analysis: `./vendor/bin/phpstan --memory-limit=2G analyse`
-   Run tests: `php artisan test`

> If you have issues committing, ensure pre-commit hooks are executable:
>
> ```bash
> chmod ug+x .husky/pre-commit
> ```

-   **Pint:** Run `./vendor/bin/pint` to auto-format code. VS Code users can bind Pint to `Ctrl+S` for instant formatting.
-   **Larastan/PHPStan:** Run `./vendor/bin/phpstan analyse` for static analysis.

---

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

---

## 📄 License

[MIT](LICENSE)

---

## 💬 Support

For questions, suggestions, or support, please open an issue or contact the maintainer.
