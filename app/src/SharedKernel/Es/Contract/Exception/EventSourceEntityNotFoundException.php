<?php

declare(strict_types=1);

namespace App\SharedKernel\Es\Contract\Exception;

use App\SharedKernel\Es\Contract\EventSourceEntityInterface;

/**
 * Exception to indicate missing events for the requested domain object.
 *
 * @psalm-pure
 */
final class EventSourceEntityNotFoundException extends \DomainException implements EventSourceExceptionInterface
{
    /** @var class-string<EventSourceEntityInterface> */
    private string $entityClass;
    private string $entityId;

    /** @param class-string<EventSourceEntityInterface> $entityClass */
    public function __construct(string $entityClass, string $entityId)
    {
        parent::__construct('Entity not found');
        $this->entityClass = $entityClass;
        $this->entityId = $entityId;
    }

    /**
     * @return class-string<EventSourceEntityInterface>
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }
}
