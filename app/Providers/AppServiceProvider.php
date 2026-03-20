<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Dedoc\Scramble\Scramble;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;
use Dedoc\Scramble\Support\Generator\OpenApi;
use App\Support\Scramble\GetQBParameterExtractor;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Configuration\ParametersExtractors;

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
        $this->configureRateLimiting();

        // Configure Passport
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        if ($this->app->runningInConsole()) {
            $this->commands([]);
        }

        Scramble::configure()
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/')
                    && ! Str::startsWith($route->uri, 'api/admin')
                    && ! Str::startsWith($route->uri, 'api/organizer')
                    && ! Str::startsWith($route->uri, 'api/usher');
            })
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(SecurityScheme::http('bearer'));
            })
            ->withParametersExtractors(function (ParametersExtractors $extractors) {
                $extractors->append([GetQBParameterExtractor::class]);
            })
            ->expose(
                ui: '/developer/docs/api',
                document: '/developer/docs/api.json',
            );

        Scramble::registerApi('admin', ['api_path' => 'api/admin'])
            ->routes(fn (Route $route) => Str::startsWith($route->uri, 'api/admin'))
            ->expose(
                ui: '/docs/admin/api',
                document: '/docs/admin/api.json',
            );

        Scramble::registerApi('organizer', ['api_path' => 'api/organizer'])
            ->routes(fn (Route $route) => Str::startsWith($route->uri, 'api/organizer'))
            ->expose(
                ui: '/docs/organizer/api',
                document: '/docs/organizer/api.json',
            );

        Scramble::registerApi('usher', ['api_path' => 'api/usher'])
            ->routes(fn (Route $route) => Str::startsWith($route->uri, 'api/usher'))
            ->expose(
                ui: '/docs/usher/api',
                document: '/docs/usher/api.json',
            );
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        // Use CarbonImmutable for dates to prevent accidental mutation and ensure safe date handling
        Date::use(CarbonImmutable::class);

        // Prevent destructive commands in production environment
        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn (): ?Password => app()->isProduction()
                ? Password::min(10)
                    ->mixedCase()       // At least 1 upper and 1 lower case
                    ->numbers()         // At least 1 number
                    ->symbols()         // At least 1 special character
                : null
        );
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'You have exceeded the maximum number of allowed requests. Please try again later.',
                    ], 429, $headers);
                });
        });
    }
}
