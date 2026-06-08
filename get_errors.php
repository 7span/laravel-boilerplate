<?php
$content = file_get_contents('phpstan_errors.json');
$start = strpos($content, '{');
if ($start === false) {
    echo "No JSON found\n";
    exit(1);
}
$json_string = substr($content, $start);
$json = json_decode($json_string, true);

if (!isset($json['files'])) {
    echo "No errors or invalid JSON!\n";
    exit(0);
}

$files = [];
foreach ($json['files'] as $file => $data) {
    if (strpos($file, 'vendor') !== false) {
        continue;
    }
    // PHPStan sometimes outputs errors "in context of class X" for traits
    $file = preg_replace('/ \(in context of class .*\)/', '', $file);
    if (!isset($files[$file])) $files[$file] = 0;
    $files[$file] += count($data['messages']);
}
arsort($files);
$i = 0;
$out = "Total Errors: " . $json['totals']['file_errors'] . "\n\n";
foreach ($files as $file => $count) {
    $out .= "$count errors in $file\n";
    if (++$i >= 50) break;
}
file_put_contents('top_errors.txt', $out);
echo "Done writing to top_errors.txt\n";
