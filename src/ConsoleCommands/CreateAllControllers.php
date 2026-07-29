<?php

declare(strict_types=1);

namespace Medas\RestRequestHandlerGenerator\ConsoleCommands;

use Medas\Console\Commands\{
    Argument,
    BaseConsoleCommand,
    CommandInput,
    ConsoleCommandGroup,
    Option
};
use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\RestRequestHandlerGenerator\{
    ClassGenerator,
    ConfigOptions\CreateCountController,
    ConfigOptions\CreateDeleteController,
    ConfigOptions\CreateSingleVoteHandlers,
    Templates,
    VoteHandlerMethodInjector
};

#[Service]
readonly class CreateAllControllers extends BaseConsoleCommand
{
    public function __construct(
        private ClassGenerator            $classGenerator,
        private HandlerGeneratorGroup     $group,
        private Templates                 $templates,
        private VoteHandlerMethodInjector $voteHandlerMethodInjector,

        #[ConfigValue(CreateDeleteController::class)]
        private bool                      $createDeleteController = true,

        #[ConfigValue(CreateCountController::class)]
        private bool                      $createCountController = false,

        #[ConfigValue(CreateSingleVoteHandlers::class)]
        private bool                      $createSingleVoteHandlers = true,
    )
    {
    }

    public function group(): ConsoleCommandGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'create-all-controllers';
    }

    public function aliases(): array
    {
        return ['c.rest-controllers'];
    }

    public function description(): string
    {
        return 'Creates all CRUD controllers for a given entity class';
    }

    public function options(): array
    {
        return [new Option('int')];
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

        $this->requestHandlers($entityClassName, !$input->hasOption('int'));
        $this->helpers($entityClassName);
        $this->authorization($entityClassName);
    }

    private function requestHandlers(string $entityClassName, bool $useUuid): void
    {
        $replacements = [
            '{{createVoteClassName}}' => $this->classGenerator->generateClassName(
                $entityClassName,
                'Authorization\\Create',
                'Vote',
            ),
            '{{readVoteClassName}}' => $this->classGenerator->generateClassName(
                $entityClassName,
                'Authorization\\Read',
                'Vote',
            ),
            '{{updateVoteClassName}}' => $this->classGenerator->generateClassName(
                $entityClassName,
                'Authorization\\Update',
                'Vote',
            ),
            '{{deleteVoteClassName}}' => $this->classGenerator->generateClassName(
                $entityClassName,
                'Authorization\\Delete',
                'Vote',
            ),
        ];

        $this->classGenerator->generate(
            $entityClassName,
            $useUuid ? $this->templates->getInstanceByUuid() : $this->templates->getInstanceByInteger(),
            'Get',
            'Instance',
            $replacements
        );

        $this->classGenerator->generate(
            $entityClassName,
            $useUuid ? $this->templates->putInstanceByUuid() : $this->templates->putInstanceByInteger(),
            'Put',
            'Instance',
            $replacements
        );

        $this->classGenerator->generate(
            $entityClassName,
            $this->templates->createInstance(),
            'Create',
            'Instance',
            $replacements
        );

        if ($this->createDeleteController) {
            $this->classGenerator->generate(
                $entityClassName,
                $useUuid
                    ? $this->templates->deleteInstanceByUuid()
                    : $this->templates->deleteInstanceByInteger(),
                'Delete',
                'Instance',
                $replacements
            );
        }

        $this->classGenerator->generate(
            $entityClassName,
            $this->templates->getCollection(),
            'Get',
            'Collection',
            $replacements
        );

        if ($this->createCountController) {
            $this->classGenerator->generate(
                $entityClassName,
                $this->templates->getCollectionCount(),
                'Get',
                'Count',
                $replacements
            );
        }
    }

    private function helpers(string $entityClassName): void
    {
        $this->classGenerator->generate(
            $entityClassName,
            $this->templates->entityNormalizer(),
            'Helpers\\',
            'Normalizer',
            isNormalizer: true
        );
    }

    private function authorization(string $entityClassName): void
    {
        $prefixes = [
            'Create' => $this->templates->createVote(),
            'Read' => $this->templates->readDeleteVote(),
            'Update' => $this->templates->updateVote(),
            'Delete' => $this->templates->readDeleteVote(),
        ];

        if (!$this->createDeleteController) {
            unset($prefixes['Delete']);
        }

        if ($this->createSingleVoteHandlers) {
            $this->singleVoteHandlerAuthorization($entityClassName, $prefixes);
        }
        else {
            foreach ($prefixes as $prefix => $template) {
                $voteClassName = $this->classGenerator->generate(
                    $entityClassName,
                    $template,
                    'Authorization\\' . $prefix,
                    'Vote',
                );

                $this->classGenerator->generate(
                    $entityClassName,
                    $this->templates->crudVoteHandler(),
                    'Authorization\\' . $prefix,
                    'VoteHandler',
                    ['{{voteClassName}}' => $voteClassName]
                );
            }
        }
    }

    private function singleVoteHandlerAuthorization(string $entityClassName, array $prefixes): void
    {
        $handlerClassName = null;

        foreach ($prefixes as $prefix => $template) {
            $voteClassName = $this->classGenerator->generate(
                $entityClassName,
                $template,
                'Authorization\\' . $prefix,
                'Vote',
            );

            // the handler class name does not depend on $prefix, since all vote types
            // share a single handler class; whichever prefix is generated first simply
            // determines the file location for all of them
            $handlerClassName ??= $this->classGenerator->generate(
                $entityClassName,
                $this->templates->singleVoteHandler(),
                'Authorization\\',
                'VoteHandler',
            );

            $this->voteHandlerMethodInjector->addMethodIfMissing($handlerClassName, 'handle' . $prefix, $this->templates->singleVoteHandlerMethod(), [
                '{{methodName}}' => 'handle' . $prefix,
                '{{voteClassName}}' => $voteClassName,
            ]);
        }
    }
}
