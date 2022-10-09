<?php

declare(strict_types=1);

namespace Test\App\SharedKernel\Es\EntityExample;

use App\Es\Contract\EventInterface;

/**
 * {@inheritDoc}
 *
 * @psalm-immutable
 */
final class TransactionEvent implements EventInterface
{
    /** @var int */
    private int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    /** {@inheritDoc} */
    public static function fromArray(array $eventData): self
    {
        return new self($eventData['amount']);
    }

    /** {@inheritDoc} */
    public function toArray(): array
    {
        return ['amount' => $this->amount];
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
