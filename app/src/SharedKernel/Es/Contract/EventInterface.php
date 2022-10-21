<?php

declare(strict_types=1);

namespace App\SharedKernel\Es\Contract;

/**
 * Recorded ES event.
 *
 * @psalm-immutable
 */
interface EventInterface
{
    /**
     * Recreate this object from the array.
     */
    public static function fromArray(array $eventData): self;

    /**
     * Convert stored data to an array.
     */
    public function toArray(): array;
}
