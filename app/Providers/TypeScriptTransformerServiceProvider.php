<?php

declare(strict_types = 1);

namespace App\Providers;

use Spatie\TypeScriptTransformer\Formatters\PrettierFormatter;
use Spatie\TypeScriptTransformer\Transformers\EnumTransformer;
use Spatie\TypeScriptTransformer\Writers\GlobalNamespaceWriter;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfigFactory;
use Spatie\TypeScriptTransformer\Transformers\AttributedClassTransformer;
use Spatie\LaravelTypeScriptTransformer\TypeScriptTransformerApplicationServiceProvider as BaseTypeScriptTransformerServiceProvider;

class TypeScriptTransformerServiceProvider extends BaseTypeScriptTransformerServiceProvider
{
    protected function configure(TypeScriptTransformerConfigFactory $config): void
    {
        $config
            ->transformer(AttributedClassTransformer::class)
            ->transformer(EnumTransformer::class)
            ->transformDirectories(app_path())
            ->outputDirectory(resource_path())
            ->writer(new GlobalNamespaceWriter('types/generated.d.ts'))
            ->formatter(PrettierFormatter::class);
    }
}
