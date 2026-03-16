<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Revolution\Google\Sheets\Facades\Sheets;

class LangPullFromSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:pull-from-sheet {--locales= : Comma-separated locales to import (default: app.locale)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull language data from Google Sheets (dynamic sheets, columns per locale) and merge into lang files';

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

        $this->info('Importing locales: '.implode(', ', $locales));

        // Get sheet/tab names dynamically from the spreadsheet.
        $sheetTitles = Sheets::spreadsheet($spreadsheetId)->sheetList();

        if ($sheetTitles === [] || $sheetTitles === null) {
            $this->warn('No sheets found in spreadsheet. Nothing to import.');

            return self::SUCCESS;
        }

        foreach ($sheetTitles as $sheetTitle) {
            $sheet = Sheets::spreadsheet($spreadsheetId)->sheet($sheetTitle);

            /** @var array<int, array<int, string>> $rows */
            $rows = $sheet->all();

            if ($rows === [] || count($rows) === 1) {
                $this->warn('No data found in sheet ['.$sheetTitle.'] to import, skipping sheet.');

                continue;
            }

            // Expect header: key | {locale1} | {locale2} | ...
            $header = array_map('strtolower', $rows[0]);
            $keyIndex = array_search('key', $header, true);
            if ($keyIndex === false) {
                $this->error('Invalid header in sheet ['.$sheetTitle.']. Expected first column: key.');

                continue;
            }

            // Build a map of locale => column index.
            $localeIndexes = [];

            foreach ($locales as $locale) {
                $index = array_search(strtolower($locale), $header, true);

                if ($index === false) {
                    $this->warn('Locale column ['.$locale.'] not found in sheet ['.$sheetTitle.'], skipping this locale for this group.');

                    continue;
                }

                $localeIndexes[$locale] = $index;
            }

            if ($localeIndexes === []) {
                $this->warn('No matching locale columns found in sheet ['.$sheetTitle.'], skipping sheet.');

                continue;
            }

            // For each locale column, load existing lang file, update/insert keys and write back.
            foreach ($localeIndexes as $locale => $index) {
                $langPath = resource_path('lang/'.$locale);

                if (! File::isDirectory($langPath)) {
                    File::makeDirectory($langPath, 0o755, true);
                }

                $filePath = $langPath.'/'.$sheetTitle.'.php';

                /** @var array<string, mixed> $existing */
                $existing = File::exists($filePath) ? (require $filePath) : [];

                foreach (array_slice($rows, 1) as $row) {
                    $key = (string) ($row[$keyIndex] ?? '');

                    if ($key === '') {
                        continue;
                    }

                    $value = (string) ($row[$index] ?? '');

                    if ($value === '') {
                        continue;
                    }

                    // Update existing keys or insert new ones using dot-notation.
                    $this->setNestedValue($existing, $key, $value);
                }

                $content = "<?php\n\nreturn ".$this->exportArrayShort($existing).";\n";
                File::put($filePath, $content);
                $this->line('Merged lang file for locale ['.$locale.'] from sheet ['.$sheetTitle.']: '.$filePath);
            }
        }

        $this->info('Translations pulled from Google Sheets and merged into lang files for locales: '.implode(', ', $locales).'.');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $array
     */
    protected function setNestedValue(array &$array, string $key, string $value): void
    {
        $keys = explode('.', $key);

        $current = &$array;

        foreach ($keys as $segment) {
            if (! isset($current[$segment]) || ! is_array($current[$segment])) {
                $current[$segment] = [];
            }

            $current = &$current[$segment];
        }

        $current = $value;
    }

    /**
     * Export array using short syntax [] with indentation.
     *
     * @param  array<string, mixed>  $data
     */
    protected function exportArrayShort(array $data, int $indentLevel = 0): string
    {
        $indent = str_repeat('    ', $indentLevel);
        $nextIndent = str_repeat('    ', $indentLevel + 1);

        $lines = ['['];

        foreach ($data as $key => $value) {
            $keyPart = var_export($key, true). ' => ';

            if (is_array($value)) {
                $lines[] = $nextIndent.$keyPart.$this->exportArrayShort($value, $indentLevel + 1).',';
            } else {
                $valuePart = var_export($value, true);
                $lines[] = $nextIndent.$keyPart.$valuePart.',';
            }
        }

        $lines[] = $indent.']';

        return implode("\n", $lines);
    }
}

