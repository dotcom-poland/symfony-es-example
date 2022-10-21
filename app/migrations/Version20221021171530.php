<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221021171530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Event Sourcing event storage table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('event_stream');
        $table->addColumn('event_id', 'string', ['length' => 26, 'fixed' => true]);
        $table->addColumn('entity_class', 'string', ['length' => 255]);
        $table->addColumn('entity_id', 'string', ['length' => 26, 'fixed' => true]);
        $table->addColumn('event_class', 'string', ['length' => 255]);
        $table->addColumn('event_data', 'json');

        $table->setPrimaryKey(['event_id']);
        $table->addIndex(['entity_id', 'entity_class'], 'event_stream_entity');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('event_stream');
    }
}
