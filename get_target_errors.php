<?php
$json = json_decode(file_get_contents('phpstan_errors.json'), true);
$out = '';
$targets = [
    'Media/Resource.php',
    'Notification.php',
    'DeleteTempFiles.php',
    'DeveloperController.php',
    'MediaHelper.php',
    'UserOneSignalChannel.php',
    'NotificationController.php',
    'Notification/Resource.php',
    'WelcomeUser.php',
    'UserOtp.php',
    'MediaRule.php',
    'AuthService.php',
    'CountryService.php',
    'LanguageService.php',
    'SettingService.php',
];
foreach ($json['files'] as $f => $d) {
    foreach ($targets as $t) {
        if (strpos($f, str_replace('/', DIRECTORY_SEPARATOR, $t)) !== false) {
            $out .= "=== $f ===\n";
            foreach ($d['messages'] as $m) {
                $out .= "Line {$m['line']}: {$m['message']}\n";
            }
            break;
        }
    }
}
file_put_contents('target_errors.txt', $out);
echo "Done\n";
