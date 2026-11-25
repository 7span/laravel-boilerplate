<?php

use App\Models\User;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Notifications\ForgotPasswordOtp;

Route::get('/', function () {
    return view('welcome');
});

// TEMPORARY, local only. Sends a mail to an unreachable SMTP host so the failure is
// written to storage/logs/mail-failed-*.log. Remove after checking, together with App\Mail\TestMail.
//   /test/mail-failure/notification  sends the ForgotPasswordOtp notification
//   /test/mail-failure/mailable      sends the TestMail Mailable
if (app()->isLocal()) {
    Route::get('test/mail-failure/{type}', function (string $type): void {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'unreachable.mail.test',
            'mail.mailers.smtp.port' => 2525,
            'mail.mailers.smtp.timeout' => 5,
        ]);

        if ($type === 'mailable') {
            Mail::to('billing@example.com')->send(new TestMail);

            return;
        }

        $user = (new User)->forceFill(['first_name' => 'Riya', 'last_name' => 'Patel', 'email' => 'riya@example.com']);
        $user->notifyNow(new ForgotPasswordOtp('482913'));
    })->whereIn('type', ['notification', 'mailable']);
}
