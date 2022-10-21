<?php

declare(strict_types=1);

namespace App\SharedKernel\Es\Implementation;

use App\SharedKernel\Es\Contract\EventInterface;
use App\SharedKernel\Es\Contract\EventSourceEntityInterface;
use App\SharedKernel\Es\Contract\EventSourceStoreInterface;
use App\SharedKernel\Es\Contract\Exception\EventSourceEntityNotFoundException;
use App\SharedKernel\Es\Contract\Exception\EventSourceException;

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

        try {
            foreach ($events as $event) {
                $this->events[$entityClass][$entityId][] = [
                    \get_class($event),
                    $event->toArray(),
                ];
            }
        } catch (\Throwable $exception) {
            throw new EventSourceException($exception, $entityClass, $entityId);
        }
    }

    /** {@inheritDoc} */
    public function restore(
        string $entityClass,
        string $entityId
    ): iterable {
        $eventsData = $this->events[$entityClass][$entityId] ?? [];

        if (empty($eventsData)) {
            throw new EventSourceEntityNotFoundException($entityClass, $entityId);
        }

        try {
            foreach ($eventsData as [$eventClass, $eventPayload]) {
                yield $eventClass::fromArray($eventPayload);
            }
        } catch (\Throwable $exception) {
            throw new EventSourceException($exception, $entityClass, $entityId);
        }
    }
}
