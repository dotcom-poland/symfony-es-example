<?php

declare(strict_types=1);

namespace App\SharedKernel\Es;

use App\Es\Contract\EventSourceEntityInterface;
use App\Es\Contract\EventSourceManagerInterface;
use App\Es\Contract\EventSourceStoreInterface;

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
        $this->eventSourceStore->store(
            \get_class($entity),
            $entity->getEventSourceIdentifier(),
            $entity->popEventSourceEvents(),
        );
    }

    /** {@inheritDoc} */
    public function reconstitute(
        string $entityClass,
        string $entityId
    ): EventSourceEntityInterface {
        return $entityClass::create(
            $entityId,
            new DefaultEventCollection(),
            $this->eventSourceStore->restore($entityClass, $entityId)
        );
    }
}
