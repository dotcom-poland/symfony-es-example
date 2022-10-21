<?php

declare(strict_types=1);

namespace Test\App\SharedKernel\Es\Implementation;

use App\SharedKernel\Es\Implementation\EventSourceStoreDbal;
use Doctrine\DBAL\Driver\PDO\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Factory\UlidFactory;
use Test\App\SharedKernel\Es\Implementation\EntityExample\BankAccount;
use Test\App\SharedKernel\Es\Implementation\EntityExample\TransactionEvent;

final class EventSourceStoreDbalTest extends TestCase
{
    private readonly EventSourceStoreDbal $store;

    protected function setUp(): void
    {
        $connection = new Connection(new \PDO('sqlite::memory:'));
        $connection->exec(<<<SQL
            CREATE TABLE event_stream (
                event_id char(26) not null primary key,
                entity_class TEXT not null,
                entity_id char(26) not null,
                event_class TEXT not null,
                event_data json not null
            );
        SQL
        );

        $this->store = new EventSourceStoreDbal(
            $connection,
            new UlidFactory(),
        );
    }

    public function testItRestoresPersistedEvents(): void
    {
        $this->store->store(BankAccount::class, '1', [
            new TransactionEvent(1200),
            new TransactionEvent(-200),
        ]);

        $restoredEvents = \iterator_to_array(
            $this->store->restore(BankAccount::class, '1')
        );

        self::assertEquals([
            new TransactionEvent(1200),
            new TransactionEvent(-200),
        ], $restoredEvents);
    }
}
