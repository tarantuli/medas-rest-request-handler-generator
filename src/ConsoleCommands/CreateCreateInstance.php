<?php

declare(strict_types=1);

namespace Medas\RestRequestHandlerGenerator\ConsoleCommands;

use Medas\Console\Commands\{Argument, BaseConsoleCommand, CommandInput, ConsoleCommandGroup};
use Medas\Core\Attributes\Service;
use Medas\RestRequestHandlerGenerator\{ClassGenerator, Templates};

#[Service]
readonly class CreateCreateInstance extends BaseConsoleCommand
{
    public function __construct(
        private ClassGenerator        $classGenerator,
        private HandlerGeneratorGroup $group,
        private Templates             $templates,
    )
    {
    }

    public function group(): ConsoleCommandGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'create-create-instance';
    }

    public function aliases(): array
    {
        return ['c.create-instance'];
    }

    public function description(): string
    {
        return 'Creates a POST instance method for a given entity class';
    }

    public function arguments(): array
    {
        return [
            Argument::required('entityClassName'),
        ];
    }

    public function process(CommandInput $input): void
    {
        $entityClassName = $input->getArgument('entityClassName');

        $this->classGenerator->generate(
            $entityClassName,
            $this->templates->createInstance(),
            'Create',
            '',
        );
    }
}
