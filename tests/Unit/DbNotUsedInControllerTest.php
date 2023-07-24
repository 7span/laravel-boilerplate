<?php

use Tests\TestCase;

class DbNotUsedInControllerTest extends TestCase
{
    public $excludedControllers = [
        'App\Http\Controllers\Controller',
        'App\Http\Controllers\Developer\DeveloperController',
    ];

    /**
     * @test
     */
    public function db_should_be_not_used_in_controllers()
    {
        $controllersPath = app_path('Http/Controllers');

        $this->browseDirectory($controllersPath);
    }

    private function browseDirectory($path)
    {
        $files = scandir($path);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $path . DIRECTORY_SEPARATOR . $file;

            if (is_dir($filePath)) {
                $this->browseDirectory($filePath);
            } elseif (is_file($filePath)) {
                $this->assertControllerDoesNotUseDb($filePath);
            }
        }
    }

    private function assertControllerDoesNotUseDb($filePath)
    {
        $fileContents = file_get_contents($filePath);

        $namespace = $this->getNamespaceFromContents($fileContents);

        $imports = $this->getImportsFromContents($fileContents);

        $class = $this->getClassFromContents($fileContents);

        if ($namespace && $class) {
            $controller = $namespace . '\\' . $class;

            // Exclude the controller file from the check
            if (in_array($controller, $this->excludedControllers)) {
                return true;
            }

            // Start - Assert that there's no database usage in the controller file
            expect(in_array('DB', $imports))->toBeFalse("Found DB import in {$controller}");

            expect(in_array('Illuminate\Support\Facades\DB', $imports))->toBeFalse("Found DB import in {$controller}");

            $hasDbUsage = str_contains($fileContents, 'DB::');
            expect($hasDbUsage)->toBeFalse("Found DB usage in {$controller}");
            // End - Assert that there's no database usage in the controller file
        }
    }

    private function getNamespaceFromContents($contents)
    {
        $namespacePattern = "/namespace\s+([^\s;]+)/";

        preg_match($namespacePattern, $contents, $matches);

        return $matches[1] ?? '';
    }

    private function getClassFromContents($contents)
    {
        $classPattern = "/class\s+([^\s{]+)/";

        preg_match($classPattern, $contents, $matches);

        return $matches[1] ?? '';
    }

    private function getImportsFromContents($contents)
    {
        $usePattern = '/^use\s+(.*?);/m';

        preg_match_all($usePattern, $contents, $matches);

        return $matches[1] ?? '';
    }
}
