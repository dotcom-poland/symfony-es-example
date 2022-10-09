<?php

declare(strict_types=1);

namespace App\Es\Contract;

use IteratorAggregate;

/**
 * Collection to hold recorded ES events.
 *
 * @template-extends IteratorAggregate<array-key, EventInterface>
 */
interface EventCollectionInterface extends IteratorAggregate
{
    /**
     * Append an event to this collection.
     */
    public function record(EventInterface $event): void;

    /**
     * Clear this collection and return stored events.
     */
    public function popEvents(): self;
}
