---
name: laravel-mailable-system
description: Build production-grade Laravel email systems using structured Mailables, Blade templates, localization-first design, and queue-based delivery. Use when creating, sending, or reviewing emails with standards for scalability, consistency, and clean architecture. Triggers on "send email in Laravel", "create Mailable", "Laravel email setup", "email localization", "queue emails", or "improve Laravel email code quality".
---

# Laravel Mailable System

Build scalable, reusable, and testable email systems in Laravel using production-grade Mailables.

## Quick Start

When the user requests an email system, follow this workflow:

1. **Clarify the email purpose** - one mailable per email purpose (welcome, reset OTP, etc.).
2. **Create the Mailable class** - define `envelope()` (subject) + `content()` (Blade view + data).
3. **Create the Blade template** - extend `emails.layouts.master` and only render with provided variables.
4. **Make it queue-first** - implement `ShouldQueue` and send via `Mail::to(...)->queue(...)`.
5. **Localize strings** - use `resources/lang/<locale>/email.php` keys for email text and subject lines.
6. **Send from a service/job** - never send directly from controllers.

## Core Architecture Principles

1. **Single Responsibility** - one `Mailable` = one email purpose.
2. **Module-based structure** - organize Mailables, views, and translations by domain/module using a consistent path pattern.  
   Example: `{Module}{Action}Mail` → `emails/{module}/{action}.blade.php` → `email.{module}.{action}.*`
3. **Template-only views** - Blade templates are for presentation only; no business logic.
4. **Localization by default** - subject + body text should come from `resources/lang/*/email.php`.

## Building a New Mailable

### Step 1: Create the Mailable

Prefer using Artisan:

- Follow module-based naming: `php artisan make:mail {Module}/{Action}Mail`
Example: `User/WelcomeMail` → `App\Mail\User`, view `emails/user/welcome.blade.php`, translation `email.user.welcome.*`

### Step 2: Implement Queue-First Mailables

Every mailable should:

- implement `ShouldQueue`
- use `Queueable, SerializesModels`

### Step 3: Define `envelope()`

- Use `Illuminate\Mail\Mailables\Envelope`
- Subject must come from language files (`resources/lang/.../email.php`)

Pattern:

```php
public function envelope(): Envelope
{
    return new Envelope(
        subject: __('email.{module}.subject', ['app_name' => config('app.name')]),
    );
}
```

### Step 4: Define `content()`

- Use `Illuminate\Mail\Mailables\Content`
- Use the Blade view naming convention: `emails.<template-slug>`
- Pass only data needed by the view using the `with: [...]` array

Pattern:

```php
public function content(): Content
{
    return new Content(
        view: 'emails.some-template',
        with: [
            'name' => $this->name,
            // other template variables...
        ],
    );
}
```

### Step 5: Attachments (Optional)

Keep attachments encapsulated in the mailable:

- return type: `public function attachments(): array`
- return `[]` if none

### Step 6: Sending (Where it should happen)

Understand the user's requirement and Call `Mail::to(...)->send(...)` from where the user has specified.

Preferred:

- send via service layer or job

When you need error handling:

- handle/log inside the sending action/service (not the controller)
- ensure failures do not break the API response unless that is explicitly required by product logic

## Email Templates Standards

### Template Requirements

Every email Blade template must:

1. `@extends('emails.layouts.master')`
2. Render content inside `@section('content')`
3. Use translation keys (from `resources/lang/<locale>/email.php`) for text
4. Use only variables passed from the mailable (commonly `$name`, plus email-specific variables like `$otp`)

### No Business Logic in Blade

Avoid:

- complex conditional logic
- loops that format domain data (format in the mailable)
- generating OTPs, hashing values, computing expiration rules (compute in PHP and pass the result)

It is acceptable to use provided variables and simple formatting.

## Localization Standards

Text and subjects should live in:

- `resources/lang/en/email.php` (and equivalent locales if present)

Use a consistent structure by email purpose:

- `email.welcome_user.*`
- `email.forget_password.*`

## Anti-Patterns to Avoid

1. **Sending mail directly in controllers**
2. **Business logic inside Blade templates**
3. **Hardcoding email text/subject inside the PHP class**
    - subjects and content text must come from lang files
4. **Hardcoding large HTML in PHP**
    - keep HTML in Blade templates, not strings inside the mailable
5. **Mailables that are not queueable**
    - it should implement `ShouldQueue`
6. **Passing non-serializable payloads to queued mailables**
    - queued mailables must receive serializable values (or Eloquent models via `SerializesModels`)
