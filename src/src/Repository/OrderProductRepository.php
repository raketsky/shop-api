<?php
namespace App\Repository;

use App\Entity\OrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProduct[]    findAll()
 * @method OrderProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProductRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }

    /**
     * @param int $orderId
     * @return int
     */
    public function countByOrderId(int $orderId): int
    {
        return $this->count([
            'orderId' => $orderId,
        ]);
    }

    public function findOneByOrderIdAndProductId(int $orderId, int $productId): ?OrderProduct
    {
        return $this->findOneBy([
            'orderId' => $orderId,
            'productId' => $productId,
        ]);
    }

    /**
     * @param int $orderId
     * @return OrderProduct[]|array
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->findBy([
            'orderId' => $orderId,
        ]);
    }

    /**
     * @param int $orderId
     * @return array
     * @throws DBALException
     */
    public function findRawByOrderId(int $orderId): array
    {
        $sql = 'SELECT id, product_id, type, price, count FROM orders_products WHERE order_id = :order_id';
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'order_id' => $orderId,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * @param int $orderId
     * @return array
     * @throws DBALException
     */
    public function countByOrderIdAndType(int $orderId): array
    {
        $sql = 'SELECT p.type, SUM(op.count) AS count FROM orders_products op
LEFT JOIN products p ON p.id = op.product_id
WHERE order_id = :order_id
GROUP BY p.type';
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'order_id' => $orderId,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * @param OrderProduct $orderProduct
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(OrderProduct $orderProduct)
    {
        $em = $this->getEntityManager();
        $em->persist($orderProduct);
        $em->flush();
    }
}
