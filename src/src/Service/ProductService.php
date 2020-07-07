<?php
namespace App\Service;

use App\Entity\Product;
use App\Entity\User;
use App\Exception\AppException;
use App\Repository\ProductRepository;
use App\Traits\ToArrayServiceTrait;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class ProductService
{
    use ToArrayServiceTrait;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var PriceService
     */
    private $priceService;

    /**
     * @var ValidatorService
     */
    private $validator;

    /**
     * @param ProductRepository $productRepository
     * @param PriceService      $priceService
     * @param ValidatorService  $validator
     */
    public function __construct(
        ProductRepository $productRepository,
        PriceService $priceService,
        ValidatorService $validator
    ) {
        $this->productRepository = $productRepository;
        $this->priceService = $priceService;
        $this->validator = $validator;
    }

    /**
     * @param User   $user
     * @param string $title
     * @param string $type
     * @param int    $price
     * @param int    $stock
     * @return Product
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AppException
     */
    public function create(
        User $user,
        string $title,
        string $type,
        int $price,
        int $stock
    ): Product {
        $product = new Product();
        $product->setTitle($title);
        $product->setSku(strtoupper($type).'-'.$user->getId().'-'.$this->productRepository->getMaxId());
        $product->setType($type);
        $product->setPrice($price);
        $product->setStock($stock);
        $product->setUser($user);

        $this->validator->validate($product);

        $this->productRepository->save($product);

        return $product;
    }

    /**
     * @param int $id
     * @return Product
     * @throws AppException
     */
    public function findOneByIdOrFail(int $id): Product
    {
        $product = $this->productRepository->findOneById($id);
        if (!$product) {
            throw new AppException('Product['.$id.'] not found', 404);
        }

        return $product;
    }

    /**
     * @param array $productIds
     * @return array|Product[]
     */
    public function findByIds(array $productIds): array
    {
        return $this->productRepository->findByIds($productIds);
    }

    /**
     * @param User $user
     * @return array|Product[]
     */
    public function findByUser(User $user): array
    {
        return $this->productRepository->findByUserId($user->getId());
    }

    /**
     * @param Product $product
     * @return array
     */
    public function toArray(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'sku' => $product->getSku(),
            'type' => $product->getType(),
            'price' => $this->priceService->format($product->getPrice()),
            'stock' => $product->getStock(),
        ];
    }
}
