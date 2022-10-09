<?php

declare(strict_types=1);

namespace Test\App\SharedKernel\Es;

use App\SharedKernel\Es\DefaultEventCollection;
use App\SharedKernel\Es\EventSourceManager;
use App\SharedKernel\Es\EventSourceStoreArray;
use PHPUnit\Framework\TestCase;
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
}
