<!-- PROJECT BANNER -->
<p align="center">
  <img src="public/assets/img/logo.png" alt="Laravel Boilerplate Logo" width="120"/>
</p>

<h1 align="center">Laravel Boilerplate</h1>

<p align="center">
  <b>A robust starter project using <code>Laravel 12</code> for rapid, modern API development.</b><br>
  <i>Clean structure, best practices, authentication, and a suite of developer tools out of the box.</i>
</p>

<p align="center">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-12.x-red?logo=laravel&logoColor=white">
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

# 4. Generate app key
$ php artisan key:generate

# 5. Run migrations and seeders
$ php artisan migrate --seed

# 6. Start the server
$ php artisan serve
```

---

## ✨ Features & Packages

- **[Authentication (Laravel Sanctum)](https://laravel.com/docs/12.x/sanctum)**
- **[Role & Permission Management (Spatie Laravel Permission)](https://spatie.be/docs/laravel-permission/v6/introduction)**
- **[Media/File Management (Plank Mediable)](https://github.com/plank/laravel-mediable)**
- **[API Documentation (L5-Swagger)](https://github.com/DarkaOnLine/L5-Swagger)**
- **[Request Monitoring (Laravel Telescope)](https://laravel.com/docs/12.x/telescope)**
- **[Log Management (Log Viewer)](https://github.com/opcodesio/log-viewer)**
- **[Queue Monitoring (Laravel Horizon)](https://laravel.com/docs/12.x/horizon)**
- **[Performance Monitoring (Laravel Pulse)](https://laravel.com/docs/12.x/pulse)**
- **[Code Style (Laravel Pint)](https://laravel.com/docs/12.x/pint)**
- **[Static Analysis (Larastan/PHPStan)](https://github.com/larastan/larastan)**
- **[Universal Developer Panel Protection (Littlegatekeeper)](https://github.com/spatie/laravel-littlegatekeeper)**

---

## ⚙️ Custom Environment Variables

> In addition to standard Laravel variables, set these in your `.env`:

- `FRONT_WEBSITE_URL` — The URL of your frontend application
- `MASTER_PASSWORD` — Master password for privileged/admin operations
- `MASTER_OTP` — Master OTP code for bypassing OTP verification
- `DEVELOPER_USERNAME` / `DEVELOPER_PASSWORD` — Credentials for the developer panel
- `LOG_RETENTION_DAYS` — Days to retain log files (for `log:cleanup`)
- `TELESCOPE_ENABLED` — Enable/disable Laravel Telescope
- `CDN_ENABLE` — Enable/disable CDN usage for media URLs
- `CDN_URL` — The base URL of your CDN for media assets

---

## 🗂️ Custom Configuration File Structure

- `site.php` — Site-wide settings (frontend URL, pagination, roles, OTP, user status)
- `media.php` — Media/file upload settings (tags, directories, CDN, types, MIME mappings)
- `aws.php` — AWS credentials/settings for S3 and related services

---

## 🌍 Localization File Structure

Localization files are in `resources/lang/en/`:

- `email.php` — Email-related strings
- `entity.php` — Entity names/messages
- `message.php` — General messages
- `status.php` — Status labels/messages

Each file returns an array of key-value pairs for use with Laravel's `__()` and `trans()` functions.

---

## 📦 API Overview

### Supported Endpoints
- **Auth:** Register, Login, Logout, Get Profile, Forget Password (OTP), Reset Password
- **User:** Update Profile, Change Password, Change Status (Admin)
- **Country:** List countries (with filters)
- **Language:** List languages
- **Master Settings:** List and detail endpoints
- **Signed URL:** Generate signed URLs for file uploads

> API documentation is auto-generated and available at `/api/documentation` via Swagger (L5-Swagger).

### API Folder Structure
- `app/Http/Controllers/Api/` — API controllers (RESTful, thin, service-driven)
- `app/Http/Requests/` — FormRequest classes for validation
- `app/Http/Resources/` — API resource and collection transformers
- `app/Services/` — Business logic and service classes
- `app/Models/` — Eloquent models
- `app/Rules/` — Custom validation rules
- `app/Helpers/` — Helper classes

---

## 🛠️ Custom Functionality

### Custom Artisan Commands
- `php artisan log:cleanup` — Deletes log files older than the configured number of days
- `php artisan telescope:clear` — Clears all entries/data from Laravel Telescope
- `php artisan pulse:clear` — Clears all entries/data from Laravel Pulse

### Custom Validation Rules & Helpers
- **MediaRule:** Reusable validation for media/image fields (tags, mime types, nullable/required)
- **MediaHelper:** File naming, extension detection, media attachment/deletion, aggregate type detection
- **Image Optimization:** Configured via `config/mediable.php` for automatic optimization (JPEG, PNG, GIF, WebP, AVIF)

### Mail Layout Customization
- All emails use a custom Blade layout: `resources/views/emails/layouts/master.blade.php`
  - Branded header with logo
  - Localized greetings and sign-off
  - Centralized content section (`@yield('content')`)
  - Footer with copyright

---

## 🧑‍💻 Developer Tools

### Developer Panel
- `/developer/telescope` — Laravel Telescope
- `/developer/log-viewer` — Log Viewer
- `/developer/pulse` — Laravel Pulse
- `/developer/login` — Login for developer tools
- **Authentication:** Protected by `DEVELOPER_USERNAME` and `DEVELOPER_PASSWORD` in `.env`

### Pre-commit Checklist & Code Quality
- Lint staged PHP files: `npx --no-install lint-staged`
- Code style check: `./vendor/bin/pint`
- Static analysis: `./vendor/bin/phpstan --memory-limit=2G analyse`
- Run tests: `./vendor/bin/phpunit`

> If you have issues committing, ensure pre-commit hooks are executable:
> ```bash
> chmod ug+x .husky/pre-commit
> ```

- **Pint:** Run `./vendor/bin/pint` to auto-format code. VS Code users can bind Pint to `Ctrl+S` for instant formatting.
- **Larastan/PHPStan:** Run `./vendor/bin/phpstan analyse` for static analysis.

---

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

---

## 📄 License

[MIT](LICENSE)

---

## 💬 Support

For questions, suggestions, or support, please open an issue or contact the maintainer.
