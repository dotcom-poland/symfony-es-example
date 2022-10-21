<?php

declare(strict_types=1);

namespace App\SharedKernel\Es\Implementation;

use App\SharedKernel\Es\Contract\EventSourceEntityInterface;
use App\SharedKernel\Es\Contract\EventSourceManagerInterface;
use App\SharedKernel\Es\Contract\EventSourceStoreInterface;
use App\SharedKernel\Es\Contract\Exception\EventSourceException;
use App\SharedKernel\Es\Contract\Exception\EventSourceExceptionInterface;

/** {@inheritDoc} */
final class EventSourceManager implements EventSourceManagerInterface
{
    private EventSourceStoreInterface $eventSourceStore;

    public function __construct(EventSourceStoreInterface $eventSourceStore)
    {
        $this->eventSourceStore = $eventSourceStore;
    }

    /** {@inheritDoc} */
    public function persist(EventSourceEntityInterface $entity): void
    {
        try {
            $this->eventSourceStore->store(
                \get_class($entity),
                $entity->getEventSourceIdentifier(),
                $entity->popEventSourceEvents(),
            );
        } catch (EventSourceExceptionInterface $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new EventSourceException($exception, \get_class($entity), $entity->getEventSourceIdentifier());
        }
    }

    /** {@inheritDoc} */
    public function reconstitute(
        string $entityClass,
        string $entityId
    ): EventSourceEntityInterface {
        try {
            return $entityClass::create(
                $entityId,
                new DefaultEventCollection(),
                $this->eventSourceStore->restore($entityClass, $entityId)
            );
        } catch (EventSourceExceptionInterface $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new EventSourceException($exception, $entityClass, $entityId);
        }
    }
}
