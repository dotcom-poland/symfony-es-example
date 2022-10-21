<?php

declare(strict_types=1);

namespace App\Es\Contract;

use App\Es\Contract\Exception\EventSourceEntityNotFoundException;
use App\Es\Contract\Exception\EventSourceExceptionInterface;
use App\Es\Contract\Exception\EventSourcePersistenceException;

/**
 * Persistence store for event source events.
 */
interface EventSourceStoreInterface
{
    /**
     * Store ES events in a storage.
     *
     * @param class-string<EventSourceEntityInterface> $entityClass
     * @param iterable<EventInterface> $events
     *
     * @throws EventSourcePersistenceException
     * @throws EventSourceExceptionInterface
     */
    public function store(
        string $entityClass,
        string $entityId,
        iterable $events
    ): void;

    /**
     * Load ES events from a storage.
     *
     * @param class-string<EventSourceEntityInterface> $entityClass
     *
     * @return iterable<EventInterface>
     *
     * @throws EventSourceEntityNotFoundException
     * @throws EventSourcePersistenceException
     * @throws EventSourceExceptionInterface
     */
    public function restore(
        string $entityClass,
        string $entityId
    ): iterable;
}
