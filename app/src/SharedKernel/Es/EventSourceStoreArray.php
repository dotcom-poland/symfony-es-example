<?php

declare(strict_types=1);

namespace App\SharedKernel\Es;

use App\Es\Contract\EventInterface;
use App\Es\Contract\EventSourceEntityInterface;
use App\Es\Contract\EventSourceStoreInterface;

/** {@inheritDoc} */
final class EventSourceStoreArray implements EventSourceStoreInterface
{
    /** @var array<class-string<EventSourceEntityInterface>, array<string, array<array-key, array{class-string<EventInterface>, array}>>> */
    private array $events = [];

    /** {@inheritDoc} */
    public function store(
        string $entityClass,
        string $entityId,
        iterable $events
    ): void {
        if (! isset($this->events[$entityClass][$entityId])) {
            $this->events[$entityClass][$entityId] = [];
        }

        foreach ($events as $event) {
            $this->events[$entityClass][$entityId][] = [
                \get_class($event),
                $event->toArray(),
            ];
        }
    }

    /** {@inheritDoc} */
    public function restore(
        string $entityClass,
        string $entityId
    ): iterable {
        $eventsData = $this->events[$entityClass][$entityId] ?? [];

        foreach ($eventsData as [$eventClass, $eventPayload]) {
            yield $eventClass::fromArray($eventPayload);
        }
    }
}
