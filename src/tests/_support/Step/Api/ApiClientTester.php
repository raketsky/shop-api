<?php
namespace App\Tests\Step\Api;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;
use Codeception\Util\JsonArray;
use Exception;

class ApiClientTester extends ApiTester
{
    /**
     * @param array $userData
     * @return int
     * @throws Exception
     */
    public function createAccount(array $userData): int
    {
        $this->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->sendPOST('/users', $userData);
        $this->seeResponseCodeIs(HttpCode::OK);
        $this->seeResponseIsJson();
        // $this->seeResponseContains('{"full_name":"Jack Badlands","address":"2596  Adamsville Road","country":"USA","state":"Texas","city":"Harlingen","zip":"78550","phone":"956-496-1089","balance":100}');
        list($id) = $this->grabDataFromResponseByJsonPath('$.id');

        return $id;
    }

    /**
     * @param int   $userId
     * @param array $productData
     * @return int
     * @throws Exception
     */
    public function createProduct(int $userId, array $productData): int
    {
        $productData['user_id'] = $userId;

        $this->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->sendPOST('/products', $productData);
        $this->seeResponseCodeIs(HttpCode::OK);
        $this->seeResponseIsJson();
        //$this->seeResponseContains('{"full_name":"Jack Badlands","address":"2596  Adamsville Road","country":"USA","state":"Texas","city":"Harlingen","zip":"78550","phone":"956-496-1089","balance":100}');
        //$this->canSeeResponseContainsJson(['full_name' => 'Jack Badlands']);
        list($id) = $this->grabDataFromResponseByJsonPath('$.id');

        return $id;
    }

    /**
     * @param int   $userId
     * @param array $products
     * @return array
     */
    public function createOrder(int $userId, array $products): array
    {
        $this->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->sendPOST('/orders', [
            'user_id' => $userId,
            'products' => $products,
        ]);
        $this->seeResponseIsJson();
        $response = $this->grabResponse();

        return (new JsonArray($response))->toArray();
    }

    /**
     * @param int $orderId
     * @return array
     */
    public function getOrderSummary(int $orderId): array
    {
        $this->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->sendGET('/orders/'.$orderId);
        $this->seeResponseCodeIs(HttpCode::OK);
        $this->seeResponseIsJson();
        $response = $this->grabResponse();

        return (new JsonArray($response))->toArray();
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getUserProducts(int $userId): array
    {
        $this->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->sendGET('/users/'.$userId.'/products');
        $this->seeResponseCodeIs(HttpCode::OK);
        $this->seeResponseIsJson();
        $response = $this->grabResponse();

        return (new JsonArray($response))->toArray();
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getOrders(int $userId): array
    {
        $this->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->sendGET('/users/'.$userId.'/orders');
        $this->seeResponseCodeIs(HttpCode::OK);
        $this->seeResponseIsJson();
        $response = $this->grabResponse();

        return (new JsonArray($response))->toArray();
    }
}
