<?php
namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @param int $id
     * @return Order|null
     */
    public function findOneById(int $id): ?Order
    {
        return $this->findOneBy([
            'id' => $id,
        ]);
    }

    /**
     * @param int $id
     * @return Order[]|array
     */
    public function findByUserId(int $id): array
    {
        return $this->findBy([
            'userId' => $id,
        ]);
    }

    /**
     * @param Order $order
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Order $order)
    {
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
    }
}
