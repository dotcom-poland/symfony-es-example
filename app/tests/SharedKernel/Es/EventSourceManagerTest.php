<?php

declare(strict_types=1);

namespace Test\App\SharedKernel\Es;

use App\Es\Contract\Exception\EventSourceEntityNotFoundException;
use App\Es\Contract\Exception\EventSourceException;
use App\SharedKernel\Es\DefaultEventCollection;
use App\SharedKernel\Es\EventSourceManager;
use App\SharedKernel\Es\EventSourceStoreArray;
use PHPUnit\Framework\TestCase;
use Test\App\SharedKernel\Es\BrokenEntityExample\EntityWithBrokenFactory;
use Test\App\SharedKernel\Es\BrokenEntityExample\EntityWithBrokenFactoryEvent;
use Test\App\SharedKernel\Es\EntityExample\BankAccount;

final class EventSourceManagerTest extends TestCase
{
    private EventSourceManager $eventSourceManager;

    protected function setUp(): void
    {
        $this->eventSourceManager = new EventSourceManager(new EventSourceStoreArray());
    }

    public function testItRecreatesEntityFromPersistence(): void
    {
        $bankAccount = BankAccount::create(
            '123',
            new DefaultEventCollection(),
            new DefaultEventCollection(),
        );

        $bankAccount->transaction(+1000);
        $bankAccount->transaction(-2000);
        $bankAccount->transaction(+1500);

        $this->eventSourceManager->persist($bankAccount);
        unset($bankAccount);

        $bankAccount = $this->eventSourceManager->reconstitute(BankAccount::class, '123');

        self::assertEquals(+500, $bankAccount->getBalance());
    }

    public function testPersistThrowsWrappedExceptionInCaseOfAnyErrors(): void
    {
        $entity = EntityWithBrokenFactoryEvent::create('678', new DefaultEventCollection(), new DefaultEventCollection());
        $entity->recordBrokenEvent();
        $this->eventSourceManager->persist($entity);
        unset($events, $entity);

        try {
            $this->eventSourceManager->reconstitute(EntityWithBrokenFactoryEvent::class, '678');
        } catch (EventSourceException $exception) {
            self::assertEquals(EntityWithBrokenFactoryEvent::class, $exception->getEntityClass());
            self::assertEquals('678', $exception->getEntityId());
        }
    }

    public function testReconstituteThrowsWrappedExceptionInCaseOfAnyErrors(): void
    {
        $this->eventSourceManager->persist(new EntityWithBrokenFactory('1'));

        try {
            $this->eventSourceManager->reconstitute(EntityWithBrokenFactory::class, '1');
        } catch (EventSourceException $exception) {
            self::assertEquals(EntityWithBrokenFactory::class, $exception->getEntityClass());
            self::assertEquals('1', $exception->getEntityId());
        }
    }

    public function testReconstituteThrowsExceptionIfEntityCouldNotBeFound(): void
    {
        try {
            $this->eventSourceManager->reconstitute(BankAccount::class, '345');
        } catch (EventSourceEntityNotFoundException $exception) {
            self::assertEquals(BankAccount::class, $exception->getEntityClass());
            self::assertEquals('345', $exception->getEntityId());
        }
    }
}
