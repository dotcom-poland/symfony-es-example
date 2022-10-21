<?php

namespace App\SharedKernel\Es\Contract\Exception;

use App\SharedKernel\Es\Contract\EventSourceEntityInterface;
use Throwable;

/**
 * Exception contract for every ES exception.
 *
 * @psalm-pure
 */
interface EventSourceExceptionInterface extends Throwable
{
    /** @return class-string<EventSourceEntityInterface> */
    public function getEntityClass(): string;

    public function getEntityId(): string;
}
