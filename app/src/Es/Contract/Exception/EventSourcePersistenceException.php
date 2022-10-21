<?php

declare(strict_types=1);

namespace App\Es\Contract\Exception;

use App\Es\Contract\EventSourceEntityInterface;

/**
 * Exception for any persistence related errors (i.e. connection or query errors)
 *
 * @psalm-pure
 */
final class EventSourcePersistenceException extends \DomainException implements EventSourceExceptionInterface
{
    /** @var class-string<EventSourceEntityInterface> */
    private string $entityClass;
    private string $entityId;

    /** @param class-string<EventSourceEntityInterface> $entityClass */
    public function __construct(\Throwable $previous, string $entityClass, string $entityId)
    {
        parent::__construct('Error during entity persistence', 0, $previous);
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
