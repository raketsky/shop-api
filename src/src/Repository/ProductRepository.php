<?php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getMaxId(): int
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('MAX(t.id)')
            ->from($this->getClassName(), 't')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param int $id
     * @return Product|null
     */
    public function findOneById(int $id): ?Product
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @param array $productIds
     * @return Product[]|array
     */
    public function findByIds(array $productIds): array
    {
        return $this->findBy(['id' => $productIds]);
    }

    /**
     * @param int $userId
     * @return Product[]|array
     */
    public function findByUserId(int $userId): array
    {
        return $this->findBy(['userId' => $userId]);
    }

    /**
     * @param Product $product
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Product $product)
    {
        $em = $this->getEntityManager();
        $em->persist($product);
        $em->flush();
    }
}
