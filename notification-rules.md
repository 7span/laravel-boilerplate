---
name: laravel-notification-system
description: Build production-grade Laravel notifications using consistent standards, channel-driven `via()` methods, queue-first delivery, structured database payloads, and view-based mail rendering. Use when creating, sending, or reviewing notifications with emphasis on scalability, consistency, and clean architecture. Triggers on "Laravel notifications", "send notification", "database notifications", "mail notification", "queue notifications", or "improve notification code quality".
---

# Laravel Notification System

Build scalable, reusable Laravel Notifications using a production-grade approach: always define delivery channels (`via`), prefer queued delivery, and store structured data for your database channel.

## Quick Start

When the user requests a notification system, follow this workflow:

1. **Create the notification class** - one class per notification purpose (password reset, welcome, etc.).
2. **Define `via()`** - always return the delivery channels you want (`mail`, `database`, `broadcast`, etc.).
3. **Implement queueing** - implement `ShouldQueue` and use `Queueable`.
4. **Implement channel payloads** - use the correct methods per channel (for example `toMail()`, `toDatabase()`).
5. **Send the notification**
    - To send notifications (single or multiple recipients), use the `Notification` facade:
        - `Notification::send($recipientOrRecipients, new SomeNotification(...));`
6. **Localize + structure**
    - For `database` notifications, store `title` and `description` as translation keys and put variables into `data`.
    - For `mail` notifications, prefer view-based rendering over inline message lines.

## Core Architecture Principles

1. **One notification class = one notification purpose**
2. **`via()` is mandatory** - never rely on defaults for delivery channels.
3. **Queue-first** - notifications should be async.
4. **Separation of concerns**
    - Notification class = message composition and channel payloads only.
    - Business logic = service layer / job layer (not inside channel payload methods).
5. **Structured database payloads**
    - The `database` channel should receive a payload designed for your notification table.

## Project Conventions

### Notification Classes Location

- Create notification classes in `app/Notifications/`.

### Sending Notifications

- `Notification::send($user, new WelcomeUserNotification($context));`

### Queueing Notifications

Every notification should:

- `implements ShouldQueue`
- `use Queueable`

### Database Channel Payload Fields

This project should use a custom `app/Channels/DatabaseChannel.php` which writes the following fields into `app/Models/Notification`:

- `id` - taken from `$notification->id`
- `user_id` - taken from `$notifiable->id`
- `sent_by` - from payload (`sent_by`) or defaults to `Auth::id()`
- `title` - from payload (`title`) or `''`
- `description` - from payload (`description`) or `''`
- `type` - from payload (`type`) or `null`
- `notifiable_type` - from payload (`notifiable_type`) or `null`
- `notifiable_id` - from payload (`notifiable_id`) or `null`
- `data` - from payload (`data`) or `null`
- `read_at` - left for read/unread flows

Also note: `app/Http/Resources/Notification/Resource.php` treats `title` and `description` as translation keys and interpolates variables from the `data` array.

Therefore:

- Put translation keys into `title` and `description`
- Put variables for interpolation into `data`

### Mail Rendering (View-based)

When a notification supports email, implement `toMail()` and build the mail message using a Blade view rather than long static `->line()` chains.

## Building a New Notification

### Step 1: Create the Notification

Prefer Artisan:

- `php artisan make:notification Auth/WelcomeUserNotification --no-interaction`

### Step 2: Implement `via()` (Always)

Example rule:

- If the notification should be shown in-app: include `'database'`
- If it should send an email: include `'mail'`
- If it should push to clients: include `'broadcast'`

`via()` must be deterministic and explicit.

### Step 3: Queue the Notification

Example requirements:

- `class X extends Notification implements ShouldQueue`
- `use Queueable`

### Step 4: Implement `toDatabase()` for DB payloads

Your `toDatabase()` must return an array whose keys align with the fields written by `app/Channels/DatabaseChannel.php`.

Minimum recommended payload keys:

- `title`: translation key (string)
- `description`: translation key (string)
- `data`: array of interpolation variables
- `type`: notification type/category (optional)
- `notifiable_type`, `notifiable_id`: optional (only if needed)

### Step 5: Implement `toMail()` using a view

Rules for `toMail()`:

- Use view-based rendering (preferred) instead of many static lines.
- Pass only the variables that the Blade template requires.
- Keep templates in `resources/views/emails/*` and reuse your existing `emails.layouts.master` layout.

### Step 6: Factor repeated mail-building logic

If you need the same mail variables, subjects, or view selection in multiple places inside the notification class:

- Extract shared logic into a private method on the notification class, or
- Extract into a trait used by multiple notification classes.

Do not duplicate complex array-shaping logic across methods.

## Anti-Patterns to Avoid

1. **Not writing `via()` method**
2. **Sending mail content via static `->line()` chains** when a view exists in your email templates system
3. **Forgetting queueing**
    - avoid notifications that are not async (`ShouldQueue` + `Queueable`)
4. **Unstructured database payloads**
    - don’t store final text in `title`/`description` if the UI expects translation keys
5. **Hardcoded interpolation values**
    - interpolation should come from the `data` payload array so the resource can translate consistently
6. **Passing non-serializable objects into queued notifications**
    - only store serializable scalar data or Eloquent models via `SerializesModels`-friendly patterns
7. **Business logic inside channel payload methods**
    - keep computations and side effects in services/jobs; notifications should only compose channel outputs
