<?php

declare(strict_types=1);

namespace Medas\RestRequestHandlerGenerator;

use Medas\Core\{Attributes\Service, Identifier};
use Medas\EntityGenerator\{NameConverters\NameConverter, Pluralizers\Pluralizer};

#[Service]
readonly class KebabCaseNames implements NameConverter
{
    public function __construct(
        private Pluralizer $pluralizer,
    )
    {
    }

    public function convert(string $name): string
    {
        return new Identifier($this->pluralizer->pluralize($name))->toKebabCase();
    }
}
