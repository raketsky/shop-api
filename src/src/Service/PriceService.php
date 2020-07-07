<?php
namespace App\Service;

class PriceService
{
    /**
     * @param int $price
     * @return string
     */
    public function format(int $price): string
    {
        return number_format($this->toFloat($price), 2).' $';
    }

    /**
     * @param int $price
     * @return float
     */
    public function toFloat(int $price): float
    {
        return $price / 100;
    }

    /**
     * @param float $price
     * @return float
     */
    public function toInt(float $price): float
    {
        return $price * 100;
    }
}
