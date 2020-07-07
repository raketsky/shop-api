<?php
namespace App\Tests;

use App\Entity\Product;
use App\Tests\Step\Api\ApiClientTester;

class UserProductsListCest extends SimpleFlowCest
{
    public function _before(ApiClientTester $I)
    {
    }

    public function tryToTest(ApiClientTester $I)
    {
        $sellerId = $this->generatePerson($I);

        $this->generateProduct($I, $sellerId, Product::TYPE_TSHIRT, 15, 1);
        $this->generateProduct($I, $sellerId, Product::TYPE_TSHIRT, 50, 1);
        $this->generateProduct($I, $sellerId, Product::TYPE_TSHIRT, 100, 1);

        $I->getUserProducts($sellerId);

        $I->seeResponseJsonMatchesJsonPath('$..title');
        $I->seeResponseJsonMatchesJsonPath('$..price');
    }
}
