#### Notifications and Email

**Use Notification classes for all notifications and emails.**
Flag any direct `Mail::send()`, `Mail::to()->send()`, or `Mailable` dispatch that is not triggered through a Notification class. All user-facing notifications (email, push, in-app) must go through a class that implements `ShouldQueue` and extends `Notification`, dispatched via `$user->notify(new SomeNotification(...))` or `Notification::send(...)`.

Flag when:
- A `Mailable` is dispatched directly in a controller or service instead of via a Notification
- A Notification class is added but does not implement `ShouldQueue` (notifications must be queued)
- Email content is built inline (raw `Mail::raw(...)`) instead of using a Notification with a `toMail()` method
