<?php
namespace App\Tests;

use App\Entity\Product;
use App\Tests\Step\Api\ApiClientTester;

class MultipleProductsFlowCest extends SimpleFlowCest
{
    public function _before(ApiClientTester $I)
    {
    }

    public function tryToTest(ApiClientTester $I)
    {
        $sellerId = $this->generatePerson($I);

        $productId1 = $this->generateProduct($I, $sellerId, Product::TYPE_TSHIRT, 15, 1);
        $productId2 = $this->generateProduct($I, $sellerId, Product::TYPE_TSHIRT, 50, 1);
        $productId3 = $this->generateProduct($I, $sellerId, Product::TYPE_TSHIRT, 100, 1);

        $buyerId = $this->generatePerson($I);

        $products = [];
        $products[] = ['product_id' => $productId1, 'count' => 1];
        $products[] = ['product_id' => $productId2, 'count' => 1];
        $orderResponse = $I->createOrder($buyerId, $products);
        $I->cantSeeResponseContainsJson([
            'error' => 'Insufficient funds'
        ]);

        $I->getOrderSummary($orderResponse['order_id']);
        $I->seeResponseContainsJson([
            'status_name' => 'new',
            'product_price' => '65.00 $',
            'shipping_price' => '1.50 $',
            'total_price' => '66.50 $',
        ]);

        $products[] = ['product_id' => $productId3, 'count' => 1];
        $orderResponse = $I->createOrder($buyerId, $products);
        $I->seeResponseContainsJson([
            'error' => 'Insufficient funds',
        ]);
        $I->getOrderSummary($orderResponse['order_id']);
        $I->seeResponseContainsJson([
            'status_name' => 'declined',
            'product_price' => '165.00 $',
            'shipping_price' => '2.00 $',
            'total_price' => '167.00 $',
        ]);
    }
}
