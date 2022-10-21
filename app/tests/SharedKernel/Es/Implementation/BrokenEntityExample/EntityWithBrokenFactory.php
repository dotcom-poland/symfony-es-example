<?php

declare(strict_types=1);

namespace Test\App\SharedKernel\Es\Implementation\BrokenEntityExample;

use App\SharedKernel\Es\Contract\EventCollectionInterface;
use App\SharedKernel\Es\Contract\EventSourceEntityInterface;
use App\SharedKernel\Es\Implementation\DefaultEventCollection;

final class EntityWithBrokenFactory implements EventSourceEntityInterface
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /** {@inheritDoc} */
    public static function create(
        string $identifier,
        EventCollectionInterface $collection,
        iterable $pastEvents
    ): static {
        throw new \Exception();
    }

    public function getEventSourceIdentifier(): string
    {
        return $this->id;
    }

    public function popEventSourceEvents(): EventCollectionInterface
    {
        return new DefaultEventCollection();
    }
}
