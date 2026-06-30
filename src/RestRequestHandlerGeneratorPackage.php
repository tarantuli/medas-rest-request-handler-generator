<?php

declare(strict_types=1);

namespace Medas\RestRequestHandlerGenerator;

use Medas\Console\ConsolePackage;
use Medas\Core\{AsSingleton, BasePackage};
use Medas\EntityGenerator\EntityGeneratorPackage;
use Medas\PhpClassAnalysis\PhpClassAnalysisPackage;

class RestRequestHandlerGeneratorPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            ConsolePackage::instance(),
            EntityGeneratorPackage::instance(),
            PhpClassAnalysisPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
