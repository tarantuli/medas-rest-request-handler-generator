<?php

declare(strict_types=1);

namespace Medas\RestRequestHandlerGenerator;

use Medas\Core\Attributes\Service;

#[Service]
readonly class Templates
{
    public function getInstanceByUuid(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\Interfaces\Uuid as UuidType;
use Medas\EntityManager\EntityManager;
use Medas\HttpRequestHandler\Exceptions\RequestNotAuthorized;
use Medas\RestRequestHandler\Responses\EntityResponse;
use Medas\Routing\{Methods\Get, Parameters\Uuid, Route};

#[Route('{{routePath}}', endpointForEntity: \{{entityClassName}}::class)]
readonly class {{shortClassName}}
{
    public function __construct(
        private EntityManager $entityManager,
        private \{{normalizerClassName}} $normalizer,
    )
    {
    }

    #[Get(new Uuid('id'))]
    public function handle(UuidType $id): EntityResponse
    {
        /** @var \{{entityClassName}} {{instanceVariable}} */
        {{instanceVariable}} = $this->entityManager->get(\{{entityClassName}}::class, $id);

        allowElseThrow(
            $vote = new \{{readVoteClassName}}({{instanceVariable}}),
            new RequestNotAuthorized($vote->allowedAccess)
        );

        $data = $this->normalizer->normalizeAndSerialize({{instanceVariable}});

        return new EntityResponse($data);
    }
}

PHP;
    }

    public function getInstanceByInteger(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\EntityManager\EntityManager;
use Medas\HttpRequestHandler\Exceptions\RequestNotAuthorized;
use Medas\RestRequestHandler\Responses\EntityResponse;
use Medas\Routing\{Methods\Get, Parameters\Integer, Route};

#[Route('{{routePath}}', endpointForEntity: \{{entityClassName}}::class)]
readonly class {{shortClassName}}
{
    public function __construct(
        private EntityManager $entityManager,
        private \{{normalizerClassName}} $normalizer,
    )
    {
    }

    #[Get(new Integer('id'))]
    public function handle(int $id): EntityResponse
    {
        /** @var \{{entityClassName}} {{instanceVariable}} */
        {{instanceVariable}} = $this->entityManager->get(\{{entityClassName}}::class, $id);

        allowElseThrow(
            $vote = new \{{readVoteClassName}}({{instanceVariable}}),
            new RequestNotAuthorized($vote->allowedAccess)
        );

        $data = $this->normalizer->normalizeAndSerialize({{instanceVariable}});

        return new EntityResponse($data);
    }
}

PHP;
    }

    public function putInstanceByUuid(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\Interfaces\Uuid as UuidType;
use Medas\EntityManager\{EntityManager, Hydration\ValueSetter, Hydration\WriteOperation, MetaDataManager};
use Medas\HttpRequestHandler\{Exceptions\RequestNotAuthorized, RequestFactory};
use Medas\RestRequestHandler\Responses\EntityResponse;
use Medas\Routing\{Methods\Put, Parameters\Uuid, Route};

#[Route('{{routePath}}', endpointForEntity: \{{entityClassName}}::class)]
readonly class {{shortClassName}}
{
    public function __construct(
        private EntityManager $entityManager,
        private MetaDataManager $metaDataManager,
        private RequestFactory $requestFactory,
        private ValueSetter $valueSetter,
        private \{{normalizerClassName}} $normalizer,
    )
    {
    }

    #[Put(new Uuid('id'))]
    public function handle(UuidType $id): EntityResponse
    {
        /** @var \{{entityClassName}} {{instanceVariable}} */
        {{instanceVariable}} = $this->entityManager->get(\{{entityClassName}}::class, $id);

        allowElseThrow(
            $vote = new \{{readVoteClassName}}({{instanceVariable}}),
            new RequestNotAuthorized($vote->allowedAccess)
        );

        $metaData = $this->metaDataManager->get(\{{entityClassName}}::class);
        $data = $this->requestFactory->get()->bodyData->data();
        $data = $this->normalizer->unserializeAndDenormalize($data, WriteOperation::Update);

        allowElseThrow(
            $vote = new \{{updateVoteClassName}}({{instanceVariable}}, $data),
            new RequestNotAuthorized($vote->allowedAccess)
        );

        $this->valueSetter->applyWritable($metaData, {{instanceVariable}}, $data);
        $this->entityManager->flush();

        $data = $this->normalizer->normalizeAndSerialize({{instanceVariable}});

        return new EntityResponse($data);
    }
}

PHP;
    }

    public function putInstanceByInteger(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\EntityManager\{EntityManager, Hydration\ValueSetter, Hydration\WriteOperation, MetaDataManager};
use Medas\HttpRequestHandler\{Exceptions\RequestNotAuthorized, RequestFactory};
use Medas\RestRequestHandler\Responses\EntityResponse;
use Medas\Routing\{Methods\Put, Parameters\Integer, Route};

#[Route('{{routePath}}', endpointForEntity: \{{entityClassName}}::class)]
readonly class {{shortClassName}}
{
    public function __construct(
        private EntityManager $entityManager,
        private MetaDataManager $metaDataManager,
        private RequestFactory $requestFactory,
        private ValueSetter $valueSetter,
        private \{{normalizerClassName}} $normalizer,
    )
    {
    }

    #[Put(new Integer('id'))]
    public function handle(int $id): EntityResponse
    {
        /** @var \{{entityClassName}} {{instanceVariable}} */
        {{instanceVariable}} = $this->entityManager->get(\{{entityClassName}}::class, $id);

        allowElseThrow(
            $vote = new \{{readVoteClassName}}({{instanceVariable}}),
            new RequestNotAuthorized($vote->allowedAccess)
        );

        $metaData = $this->metaDataManager->get(\{{entityClassName}}::class);

        $data = $this->requestFactory->get()->bodyData->data();
        $data = $this->normalizer->unserializeAndDenormalize($data, WriteOperation::Update);

        allowElseThrow(
            $vote = new \{{updateVoteClassName}}({{instanceVariable}}, $data),
            new RequestNotAuthorized($vote->allowedAccess)
        );

        $this->valueSetter->applyWritable($metaData, {{instanceVariable}}, $data);

        $this->entityManager->flush();

        $data = $this->normalizer->normalizeAndSerialize({{instanceVariable}});

        return new EntityResponse($data);
    }
}

PHP;
    }

    public function createInstance(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\EntityManager\{EntityManager, Hydration\ValueSetter, Hydration\WriteOperation, MetaDataManager};
use Medas\HttpRequestHandler\{Exceptions\RequestNotAuthorized, RequestFactory};
use Medas\RestRequestHandler\Responses\EntityResponse;
use Medas\Routing\{Methods\Post, Route};

#[Route('{{routePath}}', endpointForEntity: \{{entityClassName}}::class)]
readonly class {{shortClassName}}
{
    public function __construct(
        private EntityManager $entityManager,
        private MetaDataManager $metaDataManager,
        private RequestFactory $requestFactory,
        private ValueSetter $valueSetter,
        private \{{normalizerClassName}} $normalizer,
    )
    {
    }

    #[Post]
    public function handle(): EntityResponse
    {
        $data = $this->requestFactory->get()->bodyData->data();
        $data = $this->normalizer->unserializeAndDenormalize($data);

        allowElseThrow(
            $vote = new \{{createVoteClassName}}($data),
            new RequestNotAuthorized($vote->allowedAccess)
        );

        {{instanceVariable}} = $this->entityManager->create(\{{entityClassName}}::class);
        $metaData = $this->metaDataManager->get(\{{entityClassName}}::class);

        $this->valueSetter->applyWritable($metaData, {{instanceVariable}}, $data);

        $this->entityManager->persist({{instanceVariable}});
        $this->entityManager->flush();

        $data = $this->normalizer->normalizeAndSerialize({{instanceVariable}});

        return new EntityResponse($data);
    }
}

PHP;
    }

    public function deleteInstanceByUuid(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\Interfaces\Uuid as UuidType;
use Medas\EntityManager\EntityManager;
use Medas\HttpRequestHandler\Exceptions\RequestNotAuthorized;
use Medas\RestRequestHandler\Responses\SuccessResponse;
use Medas\Routing\{Methods\Delete, Parameters\Uuid, Route};

#[Route('{{routePath}}', endpointForEntity: \{{entityClassName}}::class)]
readonly class {{shortClassName}}
{
    public function __construct(
        private EntityManager $entityManager,
    )
    {
    }

    #[Delete(new Uuid('id'))]
    public function handle(UuidType $id): SuccessResponse
    {
        /** @var \{{entityClassName}} {{instanceVariable}} */
        {{instanceVariable}} = $this->entityManager->get(\{{entityClassName}}::class, $id);

        allowElseThrow(
            $vote = new \{{deleteVoteClassName}}({{instanceVariable}}),
            new RequestNotAuthorized($vote->allowedAccess)
        );

        $this->entityManager->delete({{instanceVariable}});
        $this->entityManager->flush();

        return new SuccessResponse(true);
    }
}

PHP;
    }

    public function deleteInstanceByInteger(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\EntityManager\EntityManager;
use Medas\HttpRequestHandler\Exceptions\RequestNotAuthorized;
use Medas\RestRequestHandler\Responses\SuccessResponse;
use Medas\Routing\{Methods\Delete, Parameters\Integer, Route};

#[Route('{{routePath}}', endpointForEntity: \{{entityClassName}}::class)]
readonly class {{shortClassName}}
{
    public function __construct(
        private EntityManager $entityManager,
    )
    {
    }

    #[Delete(new Integer('id'))]
    public function handle(int $id): SuccessResponse
    {
        /** @var \{{entityClassName}} {{instanceVariable}} */
        {{instanceVariable}} = $this->entityManager->get(\{{entityClassName}}::class, $id);

        allowElseThrow(
            $vote = new \{{deleteVoteClassName}}({{instanceVariable}}),
            new RequestNotAuthorized($vote->allowedAccess)
        );

        $this->entityManager->delete({{instanceVariable}});
        $this->entityManager->flush();

        return new SuccessResponse(true);
    }
}

PHP;
    }

    public function getCollection(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\RestRequestHandler\{Handlers\GetCollectionHandler, Responses\CollectionResponse};
use Medas\Routing\{Methods\Get, Route};

#[Route('{{routePath}}', endpointForEntity: \{{entityClassName}}::class)]
readonly class {{shortClassName}}
{
    public function __construct(
        private \{{normalizerClassName}} $normalizer,
        private GetCollectionHandler $handler,
    )
    {
    }

    #[Get]
    public function handle(): CollectionResponse
    {
        return $this->handler->handle(
            \{{entityClassName}}::class,
            \{{readVoteClassName}}::class,
            $this->normalizer,
            // pass a constructor-injected QuerySelector instance as a 4th
            // argument if this entity supports ?query= search
        );
    }
}

PHP;
    }

    public function getCollectionCount(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\EntityManager\Repository;
use Medas\EntityManager\Selector\Selectors\AllEntities;
use Medas\RestRequestHandler\{
    Filtering\SelectorBuilder,
    Responses\ScalarResponse
};
use Medas\HttpRequestHandler\RequestFactory;
use Medas\Routing\{Methods\Get, Parameters\Constant, Route};

#[Route('{{routePath}}', endpointForEntity: \{{entityClassName}}::class)]
readonly class {{shortClassName}}
{
    public function __construct(
        private Repository $repository,
        private RequestFactory $requestFactory,
        private SelectorBuilder $selectorBuilder,
    )
    {
    }

    #[Get(new Constant('count'))]
    public function handle(): ScalarResponse
    {
        $selector = $this->selectorBuilder->build(\{{entityClassName}}::class, $this->requestFactory->get()->uri->query);
        $count = $this->repository->fetchCount($selector);

        return new ScalarResponse($count);
    }
}

PHP;
    }

    public function entityNormalizer(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\{Attributes\PreferredDefault, Attributes\Service, Interfaces\Serializer};
use Medas\EntityManager\{Hydration\WriteOperation, MetaData, MetaDataManager};
use Medas\RestRequestHandler\{
    Interfaces\EntityNormalizer,
    Serializers\RestSerializer
};

#[Service]
readonly class {{shortClassName}} implements EntityNormalizer
{
    private MetaData $metaData;

    public function __construct(
        #[PreferredDefault(RestSerializer::class)]
        private Serializer $serializer,
        MetaDataManager    $metaDataManager,
    )
    {
        $this->metaData = $metaDataManager->get(\{{entityClassName}}::class);
    }

    public function normalizeAndSerialize(object $entity): array
    {
        /** @var \{{entityClassName}} $entity */
        $data = [];

        foreach ($this->metaData->readableFields as $field) {
            $value = $field->isMethod
                ? $entity->{$field->source}()
                : $entity->{$field->source};

            $data[$field->name] = $this->serializer->serialize($value);
        }

        return $data;
    }

    public function unserializeAndDenormalize(array $data, WriteOperation $operation = WriteOperation::Create): array
    {
        $result = [];

        foreach ($this->metaData->writableFields as $field) {
            if (!array_key_exists($field->name, $data)) {
                continue;
            }

            // Honor the field's create/update scope.
            if (!($operation === WriteOperation::Create ? $field->onCreate : $field->onUpdate)) {
                continue;
            }

            $type = $this->metaData->property($field->source, ignoreUnknownProperties: true)?->type;
            $result[$field->source] = $this->serializer->unserialize($data[$field->name], $type);
        }

        return $result;
    }
}

PHP;
    }

    public function readDeleteVote(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\Events\BasicVote;

class {{shortClassName}} extends BasicVote
{
    public function __construct(
        public \{{entityClassName}} {{instanceVariable}},
    )
    {
    }
}
PHP;
    }

    public function createVote(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\Events\BasicVote;

class {{shortClassName}} extends BasicVote
{
    public function __construct(
        public array $data,
    )
    {
    }
}
PHP;
    }

    public function updateVote(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\Events\BasicVote;

class {{shortClassName}} extends BasicVote
{
    public function __construct(
        public \{{entityClassName}} {{instanceVariable}},
        public array $data,
    )
    {
    }
}
PHP;
    }

    public function crudVoteHandler(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\Attributes\{EventListener, Service};

#[Service]
readonly class {{shortClassName}}
{
    #[EventListener]
    public function handle(\{{voteClassName}} $vote): void
    {
    }
}
PHP;
    }

    public function singleVoteHandler(): string
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\Attributes\{EventListener, Service};

#[Service]
readonly class {{shortClassName}}
{
}
PHP;
    }

    public function singleVoteHandlerMethod(): string
    {
        return <<<'PHP'

    #[EventListener]
    public function {{methodName}}(\{{voteClassName}} $vote): void
    {
    }
PHP;
    }
}
