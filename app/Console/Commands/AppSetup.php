<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class AppSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage examples:
     *  php artisan app:setup
     *  php artisan app:setup local
     *  php artisan app:setup production --skip-npm --skip-serve
     *
     * @var string
     */
    protected $signature = 'app:setup
        {environment=local : Target environment (local or production)}
        {--skip-composer : Skip installing PHP dependencies via Composer}
        {--skip-npm : Skip installing and building frontend assets via NPM}
        {--skip-env : Skip creating .env from .env.example}
        {--skip-hooks : Skip configuring Git hooks (Husky)}
        {--skip-key : Skip generating the application key}
        {--skip-migrate : Skip running migrations and seeders}
        {--skip-db-engine : Skip updating the database engine in config/database.php}
        {--skip-serve : Skip starting the local development server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run application setup (local or production) in a single command.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $environment = strtolower($this->argument('environment'));

        if (! in_array($environment, ['local', 'production'], true)) {
            $this->error("Invalid environment '{$environment}'. Allowed values are: local, production.");

            return self::INVALID;
        }

        // Fancy header
        $this->info('==============================================');
        $this->info(sprintf('     🚀 Application Setup For %s Started     ', strtoupper($environment)));
        $this->info('==============================================');
        
        $this->newLine();

        $totalSteps = $environment === 'local' && ! $this->option('skip-serve') ? 8 : 7;
        $step = 1;

        // 1. Install Composer dependencies (OS-aware)
        $this->comment("Step {$step}/{$totalSteps}: Installing Composer dependencies...");
        if (! $this->option('skip-composer')) {
            if (! $this->runComposerInstall($environment)) {
                return self::FAILURE;
            }
        } else {
            $this->comment('  → Skipped by --skip-composer option.');
        }
        $this->newLine();
        $step++;

        // 2. Install Node dependencies & build assets
        $this->comment("Step {$step}/{$totalSteps}: Installing NPM dependencies & building assets...");
        if (! $this->option('skip-npm')) {
            if (! $this->runNpmBuild($environment)) {
                return self::FAILURE;
            }
        } else {
            $this->comment('  → Skipped by --skip-npm option.');
        }
        $this->newLine();
        $step++;

        // 3. Ensure .env exists
        $this->comment("Step {$step}/{$totalSteps}: Ensuring .env file exists...");
        if (! $this->option('skip-env')) {
            $this->ensureEnvFile();
        } else {
            $this->comment('  → Skipped by --skip-env option.');
        }
        $this->newLine();
        $step++;

        // 4. Configure Git hooks (Husky)
        $this->comment("Step {$step}/{$totalSteps}: Configuring Git hooks (Husky)...");
        if (! $this->option('skip-hooks')) {
            $this->configureGitHooks();
        } else {
            $this->comment('  → Skipped by --skip-hooks option.');
        }
        $this->newLine();
        $step++;

        // 5. Generate the application key
        $this->comment("Step {$step}/{$totalSteps}: Generating application key...");
        if (! $this->option('skip-key')) {
            $this->info('Running php artisan key:generate ...');
            $this->call('key:generate', ['--force' => true]);
        } else {
            $this->comment('  → Skipped by --skip-key option.');
        }
        $this->newLine();
        $step++;

        // 6. Run migrations and seeders (with confirmation)
        $this->comment("Step {$step}/{$totalSteps}: Running database migrations and seeders...");
        if (! $this->option('skip-migrate')) {
            if ($this->confirm('⚠️ This will run database migrations and seeders. Continue?', true)) {
                $this->info('Running php artisan migrate --seed ...');
                $this->call('migrate', [
                    '--seed' => true,
                    '--force' => $environment === 'production',
                ]);
            } else {
                $this->line('⚠️ Skipped migrations and seeders by user choice.');
            }
        } else {
            $this->comment('  → Skipped by --skip-migrate option.');
        }
        $this->newLine();
        $step++;

        // 7. Configure database engine via .env (DB_ENGINE)
        $this->comment("Step {$step}/{$totalSteps}: Configuring database engine from DB_ENGINE (if set)...");
        if (! $this->option('skip-db-engine')) {
            $this->updateDatabaseEngine($environment);
        } else {
            $this->comment('  → Skipped by --skip-db-engine option.');
        }
        $this->newLine();
        $step++;

        // 8. Start the local development server (local only, optional)
        if ($environment === 'local' && ! $this->option('skip-serve')) {
            $this->comment("Step {$step}/{$totalSteps}: Starting local development server...");
            $this->info('Running php artisan serve ...');
            $this->call('serve');
        } elseif ($environment === 'production') {
            $this->comment('Skipping php artisan serve for production environment.');
        } else {
            $this->comment('Skipped: php artisan serve');
        }

        $this->newLine();
        $this->info('✅ Application setup completed.');

        return self::SUCCESS;
    }

    /**
     * Run composer install with OS-specific flags.
     */
    protected function runComposerInstall(string $environment): bool
    {
        $this->info('Installing Composer dependencies...');

        $isWindows = PHP_OS_FAMILY === 'Windows';

        if ($environment === 'production') {
            $command = 'composer install --no-dev --optimize-autoloader';
        } else {
            $command = 'composer install';
        }

        if ($isWindows) {
            $command .= ' --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-posix';
        }

        return $this->runProcess($command, 'Composer install');
    }

    /**
     * Run npm install and npm run build.
     */
    protected function runNpmBuild(string $environment): bool
    {
        $this->info('Installing NPM dependencies and building assets...');

        // Use the same commands as in README
        $command = 'npm install && npm run build';

        // In many production environments you may prefer to build assets in CI,
        // but we still allow it here and let the user decide via --skip-npm.

        return $this->runProcess($command, 'NPM install & build');
    }

    /**
     * Helper to run a shell command and stream output.
     */
    protected function runProcess(string $command, string $label): bool
    {
        $process = Process::fromShellCommandline($command, base_path());
        $process->setTimeout(null);

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $this->error("{$label} failed.");

            return false;
        }

        $this->info("{$label} completed.");

        return true;
    }

    /**
     * Create .env from .env.example if it does not exist.
     */
    protected function ensureEnvFile(): void
    {
        $envPath = base_path('.env');
        $examplePath = base_path('.env.example');

        if (file_exists($envPath)) {
            $this->comment('.env file already exists. Skipping copy from .env.example.');

            return;
        }

        if (! file_exists($examplePath)) {
            $this->warn('.env.example file not found. Please create your .env manually.');

            return;
        }

        if (! copy($examplePath, $envPath)) {
            $this->error('Failed to create .env file from .env.example. Please copy it manually.');

            return;
        }

        $this->info('Created .env file from .env.example. Please review and update configuration values.');
    }

    /**
     * Configure Git hooks to use Husky directory.
     */
    protected function configureGitHooks(): void
    {
        $this->info('Configuring Git hooks path (.husky)...');

        // This command is safe to run multiple times.
        $this->runProcess('git config core.hooksPath .husky', 'Git hooks configuration');
    }

    /**
     * Configure the MySQL/MariaDB engine at runtime based on .env (no file changes).
     *
     * If DB_ENGINE is set in .env, it will be applied to mysql and mariadb connections.
     * If DB_ENGINE is not set, the engine will remain null (database.php is not modified).
     */
    protected function updateDatabaseEngine(string $environment): void
    {
        $engine = env('DB_ENGINE');

        if ($engine === null || $engine === '') {
            $this->comment('DB_ENGINE not set in .env; leaving database engine at framework default (null).');

            return;
        }

        // Apply to mysql & mariadb connections at runtime without touching config files.
        config([
            'database.connections.mysql.engine' => $engine,
            'database.connections.mariadb.engine' => $engine,
        ]);

        $this->info("Database engine set to [{$engine}] for mysql and mariadb connections (from DB_ENGINE env).");
    }
}


