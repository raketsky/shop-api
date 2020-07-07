<?php
namespace App\Service;

class AddressService
{
    /**
     * Simple check, could be more complex o.O
     *
     * @param string $country
     * @return bool
     */
    public function isDomestic(string $country): bool
    {
        return $country == 'USA';
    }
}
