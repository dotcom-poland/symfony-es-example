<?php

declare(strict_types=1);

namespace App\Es\Contract;

/**
 * Persistence manager for ES entities.
 */
interface EventSourceManagerInterface
{
    /**
     * Persist an ES entity inside a storage.
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
     */
    public function reconstitute(
        string $entityClass,
        string $entityId
    ): EventSourceEntityInterface;
}
