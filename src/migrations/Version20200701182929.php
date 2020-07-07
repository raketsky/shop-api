<?php declare(strict_types=1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200701182929 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Orders table creation';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE orders (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  full_name varchar(64) NOT NULL,
  address varchar(128) NOT NULL,
  country varchar(32) NOT NULL,
  state varchar(32) DEFAULT NULL,
  city varchar(32) NOT NULL,
  zip varchar(16) DEFAULT NULL,
  phone varchar(32) NOT NULL,
  shipping varchar(16) NOT NULL,
  user_id int(10) unsigned NOT NULL,
  status_name varchar(16) NOT NULL,
  PRIMARY KEY (id),
  KEY (status_name),
  FOREIGN KEY (user_id)
        REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE orders');
    }
}
