<?php

$directories = [
    __DIR__ . '/app',
    __DIR__ . '/bootstrap',
    __DIR__ . '/config',
    __DIR__ . '/routes',
    __DIR__ . '/tests',
    __DIR__ . '/database',
];

$addedCount = 0;
$skippedCount = 0;

foreach ($directories as $dir) {
    if (!is_dir($dir)) continue;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $content = file_get_contents($path);

            if (strpos($content, 'declare(strict_types=1);') !== false) {
                $skippedCount++;
                continue;
            }

            // Replace <?php with <?php\n\ndeclare(strict_types=1);
            $newContent = preg_replace(
                '/<\?php\s*/',
                "<?php\n\ndeclare(strict_types=1);\n\n",
                $content,
                1 // Only replace the first occurrence
            );

            if ($newContent !== $content) {
                file_put_contents($path, $newContent);
                $addedCount++;
            } else {
                $skippedCount++;
            }
        }
    }
}

echo "Added to $addedCount files.\n";
echo "Skipped $skippedCount files.\n";
