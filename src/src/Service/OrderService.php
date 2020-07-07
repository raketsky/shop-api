<?php
namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\User;
use App\Exception\AppException;
use App\Repository\OrderProductRepository;
use App\Repository\OrderRepository;
use App\Traits\ToArrayServiceTrait;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class OrderService
{
    use ToArrayServiceTrait;

    /**
     * @var AddressService
     */
    private $addressService;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderProductRepository
     */
    private $orderProductRepository;

    /**
     * @var OrderProductService
     */
    private $orderProductService;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var ShippingPriceCalculatorService
     */
    private $shippingPriceCalculatorService;

    /**
     * @var ValidatorService
     */
    private $validator;

    /**
     * @param AddressService                 $addressService
     * @param OrderRepository                $orderRepository
     * @param OrderProductRepository         $orderProductRepository
     * @param OrderProductService            $orderProductService
     * @param ProductService                 $productService
     * @param ShippingPriceCalculatorService $shippingPriceCalculatorService
     * @param ValidatorService             $validator
     */
    public function __construct(
        AddressService $addressService,
        OrderRepository $orderRepository,
        OrderProductRepository $orderProductRepository,
        OrderProductService $orderProductService,
        ProductService $productService,
        ShippingPriceCalculatorService $shippingPriceCalculatorService,
        ValidatorService $validator
    ) {
        $this->addressService = $addressService;
        $this->orderRepository = $orderRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->orderProductService = $orderProductService;
        $this->productService = $productService;
        $this->shippingPriceCalculatorService = $shippingPriceCalculatorService;
        $this->validator = $validator;
    }

    /**
     * @param Order $order
     * @return array
     * @throws DBALException
     */
    public function getSummary(Order $order): array
    {
        $orderProductCost = $this->calculateOrderProductTotalPrice($order);
        $orderShippingCost = $this->calculateOrderShippingPrice($order);

        return [
            'product_price' => $orderProductCost,
            'shipping_price' => $orderShippingCost,
            'total_price' => $orderProductCost + $orderShippingCost,
        ];
    }

    /**
     * @param User   $user
     * @param string $shipping
     * @return Order
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws AppException
     */
    public function create(User $user, string $shipping = Order::SHIPPING_STANDARD): Order
    {
        $order = new Order();
        $order->setFullName($user->getFullName());
        $order->setAddress($user->getAddress());
        $order->setCountry($user->getCountry());
        $order->setState($user->getState());
        $order->setCity($user->getCity());
        $order->setZip($user->getZip());
        $order->setPhone($user->getPhone());
        $order->setPhone($user->getPhone());
        $order->setShipping($shipping);
        $order->setUser($user);
        $order->setStatusName(Order::STATUS_NEW);

        $this->validator->validate($order);

        $this->orderRepository->save($order);

        return $order;
    }

    /**
     * @param Order   $order
     * @param Product $product
     * @param int     $count
     * @return OrderProduct
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addProductToOrder(Order $order, Product $product, int $count): OrderProduct
    {
        $orderProduct = $this->orderProductRepository->findOneByOrderIdAndProductId($order->getId(), $product->getId());
        if ($orderProduct) {
            $this->orderProductService->addCount($orderProduct, $count);
        } else {
            $orderProduct = new OrderProduct();
            $orderProduct->setOrder($order);
            $orderProduct->setProduct($product);
            $orderProduct->setType($product->getType());
            $orderProduct->setPrice($product->getPrice());
            $orderProduct->setCount($count);
        }
        $this->orderProductRepository->save($orderProduct);

        return $orderProduct;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isDomestic(Order $order): bool
    {
        return $this->addressService->isDomestic($order->getCountry());
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isExpress(Order $order): bool
    {
        return $order->getShipping() === Order::SHIPPING_EXPRESS;
    }

    /**
     * @param Order  $order
     * @param string $message
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function markAsDeclined(Order $order, $message = ''): void
    {
        $order->setStatusName(Order::STATUS_DECLINED);
        // todo alse can write $message describing why order was declined
        $this->orderRepository->save($order);
    }

    /**
     * @param int $id
     * @return Order
     * @throws AppException
     */
    public function findOneByIdOrFail(int $id): Order
    {
        $order = $this->orderRepository->findOneById($id);
        if (!$order) {
            throw new AppException('Order not found', 404);
        }

        return $order;
    }

    /**
     * @param User $user
     * @return array|Order[]
     */
    public function findByUser(User $user): array
    {
        return $this->orderRepository->findByUserId($user->getId());
    }

    /**
     * @param Order $order
     * @return array|Product[]
     * @throws DBALException
     */
    public function findOrderProducts(Order $order): array
    {
        $orderProductIds = $this->orderProductRepository->findRawByOrderId($order->getId());
        $orderProductIds = array_column($orderProductIds, 'product_id');

        return $this->productService->findByIds($orderProductIds);
    }

    /**
     * @param Order $order
     * @return array
     */
    public function toArray(Order $order): array
    {
        return [
            'order_id' => $order->getId(),
            'country' => $order->getCountry(),
            'shipping' => $order->getShipping(),
            'status_name' => $order->getStatusName(),
            'products_count' => $this->orderProductService->countByOrder($order),
        ];
    }

    /**
     * @param Order $order
     * @return int
     * @throws DBALException
     */
    private function calculateOrderProductTotalPrice(Order $order): int
    {
        $productsCountByType = $this->orderProductRepository->findRawByOrderId($order->getId());
        $productTotalPrice = 0;
        foreach ($productsCountByType as $productCountByType) {
            $productTotalPrice += $productCountByType['price'] * $productCountByType['count'];
        }

        return $productTotalPrice;
    }

    /**
     * @param Order $order
     * @return int
     * @throws DBALException
     */
    private function calculateOrderShippingPrice(Order $order): int
    {
        $productsCountByType = $this->orderProductRepository->countByOrderIdAndType($order->getId());
        $isDomesticOrder = $this->isDomestic($order);
        $isExpressShipping = $this->isExpress($order);
        $shippingTotalPrice = 0;
        foreach ($productsCountByType as $productCountByType) {
            $productType = $productCountByType['type'];
            $productCount = $productCountByType['count'];
            if ($isDomesticOrder) {
                if ($isExpressShipping) {
                    $shippingTotalPrice += $this->shippingPriceCalculatorService->calculateExpressDomestic($productType, $productCount);
                } else {
                    $shippingTotalPrice += $this->shippingPriceCalculatorService->calculateStandardDomestic($productType, $productCount);
                }
            } else {
                if ($isExpressShipping) {
                    $shippingTotalPrice += $this->shippingPriceCalculatorService->calculateExpressInternational($productType, $productCount);
                } else {
                    $shippingTotalPrice += $this->shippingPriceCalculatorService->calculateStandardInternational($productType, $productCount);
                }
            }
        }

        return $shippingTotalPrice;
    }
}
