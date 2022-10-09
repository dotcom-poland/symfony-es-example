<?php

declare(strict_types=1);

namespace Test\App\SharedKernel\Es;

use App\SharedKernel\Es\DefaultEventCollection;
use PHPUnit\Framework\TestCase;
use Test\App\SharedKernel\Es\EntityExample\TransactionEvent;

final class DefaultEventCollectionTest extends TestCase
{
    private readonly DefaultEventCollection $collection;

    protected function setUp(): void
    {
        $this->collection = new DefaultEventCollection();
    }

    public function testItCanDeduplicateEventsByReference(): void
    {
        $event = new TransactionEvent(100);

        $this->collection->record($event);
        $this->collection->record(new TransactionEvent(100));
        $this->collection->record($event);

        self::assertCount(2, $this->collection);
    }

    public function testItReturnsEventsInTheOriginalOrder(): void
    {
        $fibonacci = [1, 1, 2, 3, 5, 8];

        foreach ($fibonacci as $number) {
            $this->collection->record(new TransactionEvent($number));
        }

        $result = [];

        foreach ($this->collection as $event) {
            foreach ($event->toArray() as $number) {
                $result[] = $number;
            }
        }

        self::assertSame($fibonacci, $result);
    }

    public function testPopReturnsCurrentEvents(): void
    {
        $this->collection->record(new TransactionEvent(1));
        $this->collection->record(new TransactionEvent(2));

        $collection = $this->collection->popEvents();

        self::assertCount(2, \iterator_to_array($collection));
    }

    public function testPopRemovesClearsCollection(): void
    {
        $this->collection->record(new TransactionEvent(1));

        $this->collection->popEvents();

        self::assertEmpty(\iterator_to_array($this->collection));
    }

    public function testPopIsImmutable(): void
    {
        self::assertNotSame(
            $this->collection->popEvents(),
            $this->collection->popEvents(),
        );
    }
}
