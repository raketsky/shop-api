<?php
namespace App\Tests;

use App\Entity\Product;
use App\Service\ShippingPriceCalculatorService;
use App\Tests\UnitTester;

class ShippingPriceCalculatorServiceCest
{
    public function _before(UnitTester $I)
    {
    }

    public function tryToTest(UnitTester $I, ShippingPriceCalculatorService $calculatorService)
    {
        $price = $calculatorService->calculateStandardDomestic(Product::TYPE_MUG, 1);
        $I->assertEquals(200, $price);

        $price = $calculatorService->calculateStandardDomestic(Product::TYPE_MUG, 2);
        $I->assertEquals(300, $price);

        $price = $calculatorService->calculateStandardDomestic(Product::TYPE_MUG, 3);
        $I->assertEquals(400, $price);


        $price = $calculatorService->calculateStandardDomestic(Product::TYPE_TSHIRT, 1);
        $I->assertEquals(100, $price);

        $price = $calculatorService->calculateStandardDomestic(Product::TYPE_TSHIRT, 2);
        $I->assertEquals(150, $price);

        $price = $calculatorService->calculateStandardDomestic(Product::TYPE_TSHIRT, 3);
        $I->assertEquals(200, $price);


        $price = $calculatorService->calculateStandardInternational(Product::TYPE_MUG, 1);
        $I->assertEquals(500, $price);

        $price = $calculatorService->calculateStandardInternational(Product::TYPE_MUG, 2);
        $I->assertEquals(750, $price);

        $price = $calculatorService->calculateStandardInternational(Product::TYPE_MUG, 3);
        $I->assertEquals(1000, $price);


        $price = $calculatorService->calculateStandardInternational(Product::TYPE_TSHIRT, 1);
        $I->assertEquals(300, $price);

        $price = $calculatorService->calculateStandardInternational(Product::TYPE_TSHIRT, 2);
        $I->assertEquals(450, $price);

        $price = $calculatorService->calculateStandardInternational(Product::TYPE_TSHIRT, 3);
        $I->assertEquals(600, $price);


        $price = $calculatorService->calculateExpressDomestic(Product::TYPE_MUG, 1);
        $I->assertEquals(1000, $price);

        $price = $calculatorService->calculateExpressDomestic(Product::TYPE_MUG, 2);
        $I->assertEquals(2000, $price);

        $price = $calculatorService->calculateExpressDomestic(Product::TYPE_MUG, 3);
        $I->assertEquals(3000, $price);


        $price = $calculatorService->calculateExpressDomestic(Product::TYPE_TSHIRT, 1);
        $I->assertEquals(1000, $price);

        $price = $calculatorService->calculateExpressDomestic(Product::TYPE_TSHIRT, 2);
        $I->assertEquals(2000, $price);

        $price = $calculatorService->calculateExpressDomestic(Product::TYPE_TSHIRT, 3);
        $I->assertEquals(3000, $price);


        $price = $calculatorService->calculateExpressInternational(Product::TYPE_TSHIRT, 1);
        $I->assertEquals(-1, $price);

        $price = $calculatorService->calculateExpressInternational(Product::TYPE_MUG, 3);
        $I->assertEquals(-1, $price);
    }
}
