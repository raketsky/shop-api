<?php
namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use App\Exception\AppException;

class ShippingPriceCalculatorService
{
    /**
     * @param string $type
     * @param int    $count
     * @return int
     * @throws AppException
     */
    public function calculateStandardDomestic(string $type, int $count): int
    {
        return $this->calculate($type, $count, false, Order::SHIPPING_STANDARD);
    }

    /**
     * @param string $type
     * @param int    $count
     * @return int
     * @throws AppException
     */
    public function calculateStandardInternational(string $type, int $count): int
    {
        return $this->calculate($type, $count, true, Order::SHIPPING_STANDARD);
    }

    /**
     * @param string $type
     * @param int    $count
     * @return int
     * @throws AppException
     */
    public function calculateExpressDomestic(string $type, int $count): int
    {
        return $this->calculate($type, $count, false, Order::SHIPPING_EXPRESS);
    }

    /**
     * @param string $type
     * @param int    $count
     * @return int
     * @throws AppException
     */
    public function calculateExpressInternational(string $type, int $count): int
    {
        return $this->calculate($type, $count, true, Order::SHIPPING_EXPRESS);
    }

    /**
     * @param string $type
     * @param int    $count
     * @param bool   $isInternational
     * @param string $shippingType
     * @return int
     * @throws AppException
     */
    private function calculate(string $type, int $count, bool $isInternational, string $shippingType): int
    {
        if ($count < 1) {
            return 0;
        }

        $mapping = $this->priceMapping();
        if (!isset($mapping[$shippingType])) {
            throw new AppException('Undefined product type', 500);
        }

        if ($isInternational) {
            $mapping = $mapping[$shippingType]['international'];
        } else {
            $mapping = $mapping[$shippingType]['domestic'];
        }

        if (!$mapping) {
            return -1;
        }

        list($firstPrice, $nextPrice) = $mapping[$type];

        if ($count == 1) {
            return $firstPrice;
        } else {
            return $firstPrice + ($count - 1) * $nextPrice;
        }
    }

    /**
     * @return array
     */
    private function priceMapping(): array
    {
        return [
            Order::SHIPPING_STANDARD => [
                'international' => [
                    Product::TYPE_MUG => [500, 250],
                    Product::TYPE_TSHIRT => [300, 150],
                ],
                'domestic' => [
                    Product::TYPE_MUG => [200, 100],
                    Product::TYPE_TSHIRT => [100, 50],
                ],
            ],
            Order::SHIPPING_EXPRESS => [
                'international' => false,
                'domestic' => [
                    Product::TYPE_MUG => [1000, 1000],
                    Product::TYPE_TSHIRT => [1000, 1000],
                ],
            ],
        ];
    }
}
