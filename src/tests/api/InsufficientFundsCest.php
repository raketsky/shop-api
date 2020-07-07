<?php
namespace App\Tests;

use App\Entity\Product;
use App\Tests\Step\Api\ApiClientTester;

class InsufficientFundsCest extends SimpleFlowCest
{
    public function _before(ApiClientTester $I)
    {
    }

    public function tryToTest(ApiClientTester $I)
    {
        $sellerId = $this->generatePerson($I);
        $productId1 = $this->generateProduct($I, $sellerId, Product::TYPE_TSHIRT, 101, 1);

        $buyerId = $this->generatePerson($I);
        $products = [];
        $products[] = ['product_id' => $productId1, 'count' => 1];
        $I->createOrder($buyerId, $products);
        $I->seeResponseContainsJson([
            'error' => 'Insufficient funds'
        ]);
    }
}
