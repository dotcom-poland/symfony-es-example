<?php

declare(strict_types=1);

namespace App\SharedKernel\Es\Contract;

/**
 * An event source domain entity.
 */
interface EventSourceEntityInterface
{
    /**
     * Entity factory.
     *
     * @param iterable<EventInterface> $pastEvents
     */
    public static function create(
        string $identifier,
        EventCollectionInterface $collection,
        iterable $pastEvents
    ): static;

    /**
     * Return the ID of this entity.
     */
    public function getEventSourceIdentifier(): string;

    /**
     * Return the ES event collection and clear the collection.
     */
    public function popEventSourceEvents(): EventCollectionInterface;
}
