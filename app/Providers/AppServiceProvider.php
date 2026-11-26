<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Dedoc\Scramble\Scramble;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Laravel\Passport\Passport;
use App\Channels\DatabaseChannel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use App\Channels\UserOneSignalChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Dedoc\Scramble\Support\Generator\OpenApi;
use App\Support\Scramble\GetQBParameterExtractor;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Configuration\ParametersExtractors;
use Illuminate\Notifications\Events\NotificationSending;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Strict mode for preventing N+1 queries for development environment
        Model::shouldBeStrict(! $this->app->isProduction());

        $this->configureDefaults();
        $this->configurePassport();
        $this->configureRateLimiting();
        $this->configureApiDocumentation();
        $this->configureNotificationChannels();
    }

    protected function configurePassport(): void
    {
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }

    protected function configureDefaults(): void
    {
        // Prevent destructive commands in production environment
        DB::prohibitDestructiveCommands($this->app->isProduction());

        Password::defaults(
            fn (): ?Password => $this->app->isProduction()
                ? Password::min(10)
                    ->mixedCase()       // At least 1 upper and 1 lower case
                    ->numbers()         // At least 1 number
                    ->symbols()         // At least 1 special character
                : null
        );
    }

    /**
     * Swap the framework channels for the application ones so `via()` can keep returning the
     * `database` and `onesignal` driver names, and make both honour the notification switch.
     */
    protected function configureNotificationChannels(): void
    {
        Notification::resolved(function (ChannelManager $manager): void {
            $manager->extend('database', fn (): DatabaseChannel => new DatabaseChannel);
            $manager->extend('onesignal', fn (): UserOneSignalChannel => new UserOneSignalChannel);
        });

        // When notifications are off, block database and push. Mail still sends, so the OTP works.
        Event::listen(function (NotificationSending $event): ?bool {
            if (config('site.notification_enabled')) {
                return null;
            }

            // false cancels the send, null leaves it to other listeners.
            return in_array($event->channel, ['database', 'onesignal'], true) ? false : null;
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => __('message.too_many_requests'),
                    ], 429, $headers);
                });
        });
    }

    protected function configureApiDocumentation(): void
    {
        // The user API, which is every route outside the admin prefix.
        Scramble::configure()
            ->routes(fn (Route $route): bool => Str::startsWith($route->uri, 'api/')
                && ! Str::startsWith($route->uri, 'api/v1/admin'))
            ->withDocumentTransformers(function (OpenApi $openApi): void {
                $openApi->secure(SecurityScheme::http('bearer'));
            })
            ->withParametersExtractors(function (ParametersExtractors $extractors): void {
                $extractors->append(GetQBParameterExtractor::class);
            })
            ->expose(
                ui: '/developer/docs/api',
                document: '/developer/docs/api.json',
            );

        Scramble::registerApi('admin', ['api_path' => 'api/v1/admin'])
            ->routes(fn (Route $route): bool => Str::startsWith($route->uri, 'api/v1/admin'))
            ->withDocumentTransformers(function (OpenApi $openApi): void {
                $openApi->secure(SecurityScheme::http('bearer'));
            })
            ->withParametersExtractors(function (ParametersExtractors $extractors): void {
                $extractors->append(GetQBParameterExtractor::class);
            })
            ->expose(
                ui: '/developer/docs/admin',
                document: '/developer/docs/admin.json',
            );
    }
}
