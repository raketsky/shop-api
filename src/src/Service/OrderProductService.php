<?php
namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Repository\OrderProductRepository;

class OrderProductService
{
    /**
     * @var OrderProductRepository
     */
    private $orderProductRepository;

    /**
     * @var PriceService $priceService
     */
    private $priceService;

    /**
     * @param OrderProductRepository $orderProductRepository
     * @param PriceService           $priceService
     */
    public function __construct(
        OrderProductRepository $orderProductRepository,
        PriceService $priceService
    ) {
        $this->orderProductRepository = $orderProductRepository;
        $this->priceService = $priceService;
    }

    /**
     * @param OrderProduct $orderProduct
     * @return $this
     */
    public function incCount(OrderProduct $orderProduct)
    {
        $initialCount = $orderProduct->getCount();
        $orderProduct->setCount($initialCount + 1);

        return $this;
    }

    /**
     * @param OrderProduct $orderProduct
     * @param int          $count
     * @return $this
     */
    public function addCount(OrderProduct $orderProduct, int $count): void
    {
        $initialCount = $orderProduct->getCount();
        $orderProduct->setCount($initialCount + $count);
    }

    /**
     * @param Order $order
     * @return int
     */
    public function countByOrder(Order $order): int
    {
        return $this->orderProductRepository->countByOrderId($order->getId());
    }

    /**
     * @param OrderProduct $orderProduct
     * @return array
     */
    public function toArray(OrderProduct $orderProduct): array
    {
        return [
            'id' => $orderProduct->getId(),
            'type' => $orderProduct->getType(),
            'price' => $this->priceService->format($orderProduct->getPrice()),
            'count' => $orderProduct->getCount(),
        ];
    }

    /**
     * @param array $orderProducts
     * @return array
     */
    public function collectionToArray(array $orderProducts): array
    {
        $items = [];
        foreach ($orderProducts as $orderProduct) {
            $items[] = $this->toArray($orderProduct);
        }

        return $items;
    }
}
