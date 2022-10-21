<?php

declare(strict_types=1);

namespace Test\App\SharedKernel\Es\BrokenEntityExample;

use App\Es\Contract\EventCollectionInterface;
use App\Es\Contract\EventSourceEntityInterface;

final class EntityWithBrokenFactoryEvent implements EventSourceEntityInterface
{
    private string $id;
    private EventCollectionInterface $events;

    private function __construct(string $id, EventCollectionInterface $events)
    {
        $this->id = $id;
        $this->events = $events;
    }

    /** {@inheritDoc} */
    public static function create(
        string $identifier,
        EventCollectionInterface $collection,
        iterable $pastEvents
    ): static {
        \iterator_to_array($pastEvents);

        return new self($identifier, $collection);
    }

    public function getEventSourceIdentifier(): string
    {
        return $this->id;
    }

    public function popEventSourceEvents(): EventCollectionInterface
    {
        return $this->events->popEvents();
    }

    public function recordBrokenEvent(): void
    {
        $this->events->record(new BrokenFactoryEvent());
    }
}
