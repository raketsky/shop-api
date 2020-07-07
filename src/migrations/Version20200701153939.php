<?php declare(strict_types=1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200701153939 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'User table creation';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  full_name varchar(64) NOT NULL,
  address varchar(128) NOT NULL,
  country varchar(32) NOT NULL,
  state varchar(32) DEFAULT NULL,
  city varchar(32) NOT NULL,
  zip varchar(16) DEFAULT NULL,
  phone varchar(32) NOT NULL,
  balance int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE users');
    }
}
