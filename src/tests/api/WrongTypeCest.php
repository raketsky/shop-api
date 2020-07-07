<?php
namespace App\Tests;

use App\Tests\Step\Api\ApiClientTester;
use Codeception\Util\HttpCode;

class WrongTypeCest extends SimpleFlowCest
{
    public function _before(ApiClientTester $I)
    {
    }

    public function tryToTest(ApiClientTester $I)
    {
        $sellerId = $this->generatePerson($I);
        $productData = [
            'title' => 'Test T-Shirt',
            'type' => 'socks',
            'price' => 7.00,
            'stock' => 1,
            'user_id' => $sellerId,
        ];
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/products', $productData);
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseIsJson();
    }
}
