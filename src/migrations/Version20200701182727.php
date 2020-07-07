<?php declare(strict_types=1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200701182727 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Products table creation';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE products (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(64) NOT NULL DEFAULT "",
  sku varchar(32) NOT NULL DEFAULT "",
  type varchar(16) NOT NULL DEFAULT "",
  price int(11) NOT NULL,
  stock int(11) NOT NULL,
  user_id int(10) unsigned NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY sku (sku),
  FOREIGN KEY (user_id)
      REFERENCES users (id)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE products');
    }
}
