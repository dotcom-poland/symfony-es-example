<?php

declare(strict_types=1);

namespace Test\App\SharedKernel\Es\EntityExample;

use App\Es\Contract\EventCollectionInterface;
use App\Es\Contract\EventSourceEntityInterface;

final class BankAccount implements EventSourceEntityInterface
{
    private string $id;
    private EventCollectionInterface $events;
    private int $balance = 0;

    private function __construct(string $id, EventCollectionInterface $events)
    {
        $this->id = $id;
        $this->events = $events;
    }

    /** {@inheritDoc} */
    public static function create(
        string $identifier,
        EventCollectionInterface $collection,
        iterable $pastEvents
    ): EventSourceEntityInterface {
        $instance = new self($identifier, $collection);

        foreach ($pastEvents as $event) {
            switch (\get_class($event)) {
                case TransactionEvent::class:
                    $instance->applyTransaction($event);
                    break;

                default:
                    throw new \InvalidArgumentException();
            }
        }

        return $instance;
    }

    public function getEventSourceIdentifier(): string
    {
        return $this->id;
    }

    public function popEventSourceEvents(): EventCollectionInterface
    {
        return $this->events->popEvents();
    }

    public function transaction(int $amount): void
    {
        $event = new TransactionEvent($amount);

        $this->applyTransaction($event);

        $this->events->record($event);
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    private function applyTransaction(TransactionEvent $event): void
    {
        $this->balance += $event->getAmount();
    }
}
