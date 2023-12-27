<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            $urlArr = explode('?', $url);
            $getVars = (count($urlArr) > 1 ? '?' . $urlArr[1] : '');

            return (new MailMessage)
                ->subject(__('email.verifyEmailSubject'))
                ->line(__('email.verifyEmailLine1'))
                ->action(__('email.verifyEmailAction'), config('site.frontWebsiteUrl') . '/email/verify/' . $notifiable->id . '' . $getVars);
        });
    }
}
