<?php

declare(strict_types=1);

namespace App\Es\Contract;

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
     */
    public function restore(
        string $entityClass,
        string $entityId
    ): iterable;
}
