<?php
$j = json_decode(file_get_contents('phpstan_errors.json'), true);
$out = fopen('final_errors_manifest.txt', 'w');
foreach ($j['files'] as $file => $data) {
    if (count($data['messages']) > 0) {
        fwrite($out, "FILE: $file\n");
        foreach ($data['messages'] as $m) {
            fwrite($out, "L{$m['line']}: {$m['message']}\n");
        }
        fwrite($out, "---\n");
    }
}
fclose($out);
echo "Manifest written.\n";
