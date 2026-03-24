<?php
$json = json_decode(file_get_contents('phpstan_errors.json'), true);
$out = '';
foreach ($json['files'] as $f => $d) {
    if (strpos($f, 'MediaHelper') !== false || strpos($f, 'ResourceFilterable') !== false) {
        $out .= "=== $f ===\n";
        foreach ($d['messages'] as $m) {
            $out .= "Line {$m['line']}: {$m['message']}\n";
        }
    }
}
file_put_contents('trait_errors.txt', $out);
echo "Done\n";
