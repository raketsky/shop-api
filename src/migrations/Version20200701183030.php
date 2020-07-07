<?php declare(strict_types=1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200701183030 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Order products table creation';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE orders_products (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  order_id int(10) unsigned NOT NULL,
  product_id int(10) unsigned NOT NULL,
  type varchar(16) NOT NULL,
  price int(11) NOT NULL,
  count mediumint(8) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  FOREIGN KEY (order_id)
      REFERENCES orders (id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (product_id)
      REFERENCES products (id)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE orders_products');
    }
}
