<?php
namespace App\Tests;

use App\Entity\Product;
use App\Tests\Step\Api\ApiClientTester;
use Exception;
use Nubs\RandomNameGenerator\Alliteration as FakeNameGenerator;

class SimpleFlowCest
{
    public function _before(ApiClientTester $I)
    {
    }

    /**
     * @param ApiClientTester $I
     * @throws Exception
     */
    public function tryToTest(ApiClientTester $I)
    {
        $sellerId = $this->generatePerson($I);
        $productId = $this->generateProduct($I, $sellerId, Product::TYPE_TSHIRT, 15, 1);

        $buyerId = $this->generatePerson($I);

        $products = [
            [
                'product_id' => $productId,
                'count' => 1
            ]
        ];
        $orderResponse = $I->createOrder($buyerId, $products);

        $I->getOrderSummary($orderResponse['order_id']);
        $I->seeResponseContainsJson([
            'product_price' => '15.00 $',
            'shipping_price' => '1.00 $',
            'total_price' => '16.00 $',
        ]);
    }

    protected function generatePerson(ApiClientTester $I): int
    {
        $fullName = $this->generateFullName();
        $userData = $this->generateUserData($fullName);
        $personId = $I->createAccount($userData);
        $I->seeResponseContainsJson(['full_name' => $fullName]);

        return $personId;
    }

    /**
     * @param ApiClientTester $I
     * @param int             $sellerId
     * @param float           $price
     * @param int             $stock
     * @return int
     * @throws Exception
     */
    protected function generateProduct(ApiClientTester $I, int $sellerId, string $type, float $price, int $stock): int
    {
        $productId = $I->createProduct($sellerId, [
            'title' => 'Test T-Shirt',
            'type' => $type,
            'price' => $price,
            'stock' => $stock,
        ]);
        $I->seeResponseContainsJson([
            'type' => $type,
            'price' => number_format($price, 2).' $',
        ]);

        return $productId;
    }

    protected function generateFullName(): string
    {
        return (new FakeNameGenerator())->getName();
    }

    protected function generateUserData(string $fullName): array
    {
        return [
            'full_name' => $fullName,
            'address' => '2596 Adamsville Road',
            'country' => 'USA',
            'state' => 'Texas',
            'city' => 'Harlingen',
            'zip' => '78550',
            'phone' => '956-496-1088',
        ];
    }
}
