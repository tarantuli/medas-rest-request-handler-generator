<?php

declare(strict_types=1);

namespace Medas\RestRequestHandlerGenerator\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup};

#[Service]
readonly class HandlerGeneratorGroup implements ConfigGroup
{
    public function __construct()
    {
    }

    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'rest-request-handler-generator';
    }
}
