<?php

declare(strict_types=1);

namespace App\SharedKernel\Es\Implementation;

use App\SharedKernel\Es\Contract\EventCollectionInterface;
use App\SharedKernel\Es\Contract\EventInterface;
use SplObjectStorage;
use Traversable;

/** {@inheritDoc} */
final class DefaultEventCollection implements EventCollectionInterface
{
    /** @var SplObjectStorage<EventInterface, null> */
    private SplObjectStorage $events;

    /** @param SplObjectStorage<EventInterface, null>|null $events */
    public function __construct(SplObjectStorage $events = null)
    {
        $this->events = $events ?? new SplObjectStorage();
    }

    /** {@inheritDoc} */
    public function record(EventInterface $event): void
    {
        if (false === $this->events->contains($event)) {
            $this->events->attach($event);
        }
    }

    /** {@inheritDoc} */
    public function popEvents(): EventCollectionInterface
    {
        try {
            return new self($this->events);
        } finally {
            $this->events = new SplObjectStorage();
        }
    }

    /** {@inheritDoc} */
    public function getIterator(): Traversable
    {
        return $this->events;
    }
}
