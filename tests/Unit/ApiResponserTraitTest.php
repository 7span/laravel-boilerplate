<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Routing\Controller;

class ApiResponserTraitTest extends TestCase
{
    public $excludedControllers = [
        'App\Http\Controllers\Controller',
        'App\Http\Controllers\Developer\DeveloperController',
    ];

    /**
     * @test
     */
    public function controllers_should_use_ApiResponser_trait()
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
                $this->assertControllerUsesApiResponserTrait($filePath);
            }
        }
    }

    private function assertControllerUsesApiResponserTrait($filePath)
    {
        $fileContents = file_get_contents($filePath);

        $namespace = $this->getNamespaceFromContents($fileContents);

        $class = $this->getClassFromContents($fileContents);

        if ($namespace && $class) {
            $controller = $namespace . '\\' . $class;

            // Exclude the controller file from the check
            if (in_array($controller, $this->excludedControllers)) {
                return true;
            }

            $traits = class_uses($controller);

            $this->assertTrue(
                in_array('App\Traits\ApiResponser', $traits),
                "The controller '{$controller}' should use the ApiResponser trait."
            );
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
}
