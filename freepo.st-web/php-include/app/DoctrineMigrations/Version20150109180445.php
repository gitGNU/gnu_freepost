<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150109180445 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE salt `salt` VARCHAR(255) NOT NULL, CHANGE emailToConfirmCode `emailToConfirmCode` VARCHAR(255) DEFAULT NULL, CHANGE passwordResetCode `passwordResetCode` VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `user` CHANGE emailToConfirmCode emailToConfirmCode VARCHAR(512) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE passwordResetCode passwordResetCode VARCHAR(32) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE salt salt VARCHAR(256) NOT NULL COLLATE utf8_unicode_ci');
    }
}
