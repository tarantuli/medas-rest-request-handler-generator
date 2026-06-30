<?php

declare(strict_types=1);

namespace Medas\RestRequestHandlerGenerator\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class CreateSingleVoteHandlers implements ConfigOption
{
    public function __construct(
        private HandlerGeneratorGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'create-single-vote-handlers';
    }

    public function description(): string
    {
        return 'Whether to consolidate the create/read/update/delete vote handlers for an entity into a single '
            . 'handler class, instead of generating a separate handler class per vote type';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): true
    {
        return true;
    }
}
