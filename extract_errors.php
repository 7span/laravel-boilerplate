<?php
$j = json_decode(file_get_contents('phpstan_errors.json'), true);
foreach ($j['files'] as $file => $data) {
    if (strpos($file, 'BaseModel.php') !== false || strpos($file, 'PaginationTrait.php') !== false) {
        echo "=== $file ===" . PHP_EOL;
        foreach ($data['messages'] as $m) {
            echo "Line {$m['line']}: {$m['message']}" . PHP_EOL;
        }
    }
}
