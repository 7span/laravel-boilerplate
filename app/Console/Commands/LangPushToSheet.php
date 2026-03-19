<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Revolution\Google\Sheets\Facades\Sheets;

class LangPushToSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:push-to-sheet {--locales= : Comma-separated locales to export (default: app.locale)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push language files to Google Sheets (one sheet per group, columns per locale) using revolution/laravel-google-sheets';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $spreadsheetId = (string) config('services.google_sheets.spreadsheet_id');

        if ($spreadsheetId === '') {
            $this->error('Missing GOOGLE_SHEETS_SPREADSHEET_ID configuration.');

            return self::FAILURE;
        }

        $localesOption = $this->option('locales');
        $locales = $localesOption !== null && $localesOption !== ''
            ? array_filter(array_map('trim', explode(',', (string) $localesOption)))
            : [config('app.locale')];

        $this->info('Exporting locales: '.implode(', ', $locales));

        $baseLocale = $locales[0];
        $baseLangPath = resource_path('lang/'.$baseLocale);

        if (! File::isDirectory($baseLangPath)) {
            $this->error('Language directory not found for base locale: '.$baseLocale);

            return self::FAILURE;
        }

        // Determine groups using base locale files.
        $groups = [];

        foreach (File::files($baseLangPath) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $group = $file->getFilenameWithoutExtension();
            $groups[] = $group;
        }

        $spreadsheet = Sheets::spreadsheet($spreadsheetId);
        $existingSheets = $spreadsheet->sheetList();

        foreach ($groups as $group) {
            // Collect flattened translations per locale for this group.
            $perLocale = [];
            $allKeys = [];

            foreach ($locales as $locale) {
                $langPath = resource_path('lang/'.$locale);
                $filePath = $langPath.'/'.$group.'.php';

                if (! File::exists($filePath)) {
                    $this->warn('Missing file for locale ['.$locale.']: '.$filePath.' (skipping locale for this group).');

                    continue;
                }

                /** @var array<mixed> $translations */
                $translations = require $filePath;

                $flat = [];
                $this->flattenTranslations($translations, $flat);

                $perLocale[$locale] = $flat;
                $allKeys = array_unique(array_merge($allKeys, array_keys($flat)));
            }

            if ($perLocale === [] || $allKeys === []) {
                $this->warn('No translations found to push for group: '.$group);

                continue;
            }

            sort($allKeys);

            $header = array_merge(['key'], $locales);
            $rows = [$header];

            foreach ($allKeys as $key) {
                $row = [$key];

                foreach ($locales as $locale) {
                    $row[] = $perLocale[$locale][$key] ?? '';
                }

                $rows[] = $row;
            }

            $sheetTitle = $group;

            // Ensure sheet/tab exists for this group (email, validation, entity, message, etc.).
            if (! in_array($sheetTitle, $existingSheets, true)) {
                $this->info('Creating sheet tab: '.$sheetTitle);
                $spreadsheet->addSheet($sheetTitle);
                // Refresh sheet list so subsequent checks are accurate.
                $existingSheets = $spreadsheet->sheetList();
            }

            $this->info('Pushing '.(count($rows) - 1).' rows to Google Sheet sheet ['.$sheetTitle.']...');

            $sheet = $spreadsheet->sheet($sheetTitle);
            $sheet->clear();
            $sheet->append($rows);
        }

        $this->info('Translations pushed to Google Sheets successfully (one sheet per group, columns per locale).');

        return self::SUCCESS;
    }

    /**
     * @param  array<mixed>  $translations
     * @param  array<string, string>  $flat
     */
    protected function flattenTranslations(array $translations, array &$flat, string $prefix = ''): void
    {
        foreach ($translations as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $this->flattenTranslations($value, $flat, $fullKey);

                continue;
            }

            $flat[$fullKey] = (string) $value;
        }
    }
}

