<?php

declare(strict_types=1);

namespace App\Es\Contract;

use App\Es\Contract\Exception\EventSourceEntityNotFoundException;
use App\Es\Contract\Exception\EventSourceExceptionInterface;
use App\Es\Contract\Exception\EventSourcePersistenceException;

/**
 * Persistence manager for ES entities.
 */
interface EventSourceManagerInterface
{
    /**
     * Persist an ES entity inside a storage.
     *
     * @throws EventSourcePersistenceException
     * @throws EventSourceExceptionInterface
     */
    public function persist(
        EventSourceEntityInterface $entity
    ): void;

    /**
     * Reconstitute an ES entity from a storage.
     *
     * @template TObject of EventSourceEntityInterface
     *
     * @param class-string<TObject> $entityClass
     *
     * @return TObject
     *
     * @throws EventSourceEntityNotFoundException
     * @throws EventSourcePersistenceException
     * @throws EventSourceExceptionInterface
     */
    public function reconstitute(
        string $entityClass,
        string $entityId
    ): EventSourceEntityInterface;
}
