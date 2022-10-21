<?php

declare(strict_types=1);

namespace App\SharedKernel\Es\Implementation;

use App\SharedKernel\Es\Contract\EventInterface;
use App\SharedKernel\Es\Contract\EventSourceStoreInterface;
use App\SharedKernel\Es\Contract\Exception\EventSourcePersistenceException;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\Uid\Factory\UlidFactory;

/** {@inheritDoc} */
final class EventSourceStoreDbal implements EventSourceStoreInterface
{
    private readonly Connection $connection;
    private readonly UlidFactory $ulidFactory;

    public function __construct(Connection $connection, UlidFactory $ulidFactory)
    {
        $this->connection = $connection;
        $this->ulidFactory = $ulidFactory;
    }

    /** {@inheritDoc} */
    public function store(string $entityClass, string $entityId, iterable $events): void
    {
        try {
            $statement = $this->connection->prepare(
                'INSERT INTO event_stream (event_id, entity_class, entity_id, event_class, event_data) ' .
                'VALUES (?, ?, ?, ?, ?)'
            );

            foreach ($events as $event) {
                /** @var array<string, mixed> $eventJsonData */
                $eventJsonData = \json_encode($event->toArray(), JSON_THROW_ON_ERROR);

                $statement->execute([
                    $this->ulidFactory->create()->toBase58(),
                    $entityClass,
                    $entityId,
                    \get_class($event),
                    $eventJsonData,
                ]);
            }
        } catch (\Throwable $exception) {
            throw new EventSourcePersistenceException($exception, $entityClass, $entityId);
        }
    }

    /** {@inheritDoc} */
    public function restore(string $entityClass, string $entityId): iterable
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT event_class, event_data FROM event_stream ' .
                'WHERE entity_id = ? AND entity_class = ? ORDER BY event_id'
            );

            $result = $statement->execute([$entityId, $entityClass]);

            $events = $result->fetchAllAssociative();
        } catch (\Throwable $exception) {
            throw new EventSourcePersistenceException($exception, $entityClass, $entityId);
        }

        /**
         * @var class-string<EventInterface> $eventClass
         * @var string $eventData
         */
        foreach ($events as ['event_class' => $eventClass, 'event_data' => $eventData]) {
            try {
                /** @var array<string, mixed> $eventPayload */
                $eventPayload = \json_decode($eventData, true, 512, JSON_THROW_ON_ERROR);

                yield $eventClass::fromArray($eventPayload);
            } catch (\JsonException $exception) {
                throw new EventSourcePersistenceException($exception, $entityClass, $entityId);
            }
        }
    }
}
