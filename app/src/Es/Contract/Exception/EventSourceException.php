<?php

declare(strict_types=1);

namespace App\Es\Contract\Exception;

use App\Es\Contract\EventSourceEntityInterface;

/**
 * Exception for any unspecified reason.
 *
 * @psalm-pure
 */
final class EventSourceException extends \DomainException implements EventSourceExceptionInterface
{
    /** @var class-string<EventSourceEntityInterface> */
    private string $entityClass;
    private string $entityId;

    /** @param class-string<EventSourceEntityInterface> $entityClass */
    public function __construct(\Throwable $previous, string $entityClass, string $entityId)
    {
        parent::__construct(\sprintf('Entity exception: %s', $previous->getMessage()), 0, $previous);
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
