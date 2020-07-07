<?php
namespace App\Controller;

use App\Exception\AppException;
use App\Service\OrderService;
use App\Service\PriceService;
use App\Service\ProductService;
use App\Service\UserService;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    /**
     * @param Request        $request
     * @param OrderService   $orderService
     * @param ProductService $productService
     * @param UserService    $userService
     * @return JsonResponse
     * @throws DBALException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(
        Request $request,
        OrderService $orderService,
        ProductService $productService,
        UserService $userService
    ): JsonResponse {
        try {
            $user = $userService->findByIdOrFail($request->get('user_id'));
            $order = $orderService->create($user);

            $rawOrderProducts = $request->get('products');
            if ($rawOrderProducts) {
                foreach ($rawOrderProducts as $rawOrderProduct) {
                    $product = $productService->findOneByIdOrFail($rawOrderProduct['product_id']);
                    if ($product->getStock() < $rawOrderProduct['count']) {
                        throw new AppException('Product['.$product->getId().'] out of stock', 204);
                    }
                    $orderService->addProductToOrder($order, $product, $rawOrderProduct['count']);
                }
            }

            $summary = $orderService->getSummary($order);
            $userService->decBalance($user, $summary['total_price']);

            return new JsonResponse(array_merge(
                $orderService->toArray($order),
                $summary
            ));
        } catch (AppException $e) {
            $orderService->markAsDeclined($order, $e->getMessage());

            return new JsonResponse([
                'order_id' => $order->getId(),
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * @param Request        $request
     * @param int            $orderId
     * @param OrderService   $orderService
     * @param ProductService $productService
     * @return JsonResponse
     * @throws AppException
     */
    public function addProduct(
        Request $request,
        int $orderId,
        OrderService $orderService,
        ProductService $productService
    ): JsonResponse {
        $order = $orderService->findOneByIdOrFail($orderId);
        $product = $productService->findOneByIdOrFail($request->get('product_id'));
        $orderService->addProductToOrder($order, $product, 1);

        return new JsonResponse($orderService->toArray($order));
    }

    /**
     * @param int          $orderId
     * @param OrderService $orderService
     * @param PriceService $priceService
     * @return JsonResponse
     * @throws AppException
     * @throws DBALException
     */
    public function summary(
        int $orderId,
        OrderService $orderService,
        PriceService $priceService
    ): JsonResponse {
        $order = $orderService->findOneByIdOrFail($orderId);
        $summary = $orderService->getSummary($order);
        $summary['status_name'] = $order->getStatusName();
        $summary['product_price'] = $priceService->format($summary['product_price']);
        $summary['shipping_price'] = $priceService->format($summary['shipping_price']);
        $summary['total_price'] = $priceService->format($summary['total_price']);

        return new JsonResponse($summary);
    }

    /**
     * @param Request      $request
     * @param int          $userId
     * @param OrderService $orderService
     * @param UserService  $userService
     * @return JsonResponse
     * @throws AppException
     */
    public function listByUser(
        Request $request,
        int $userId,
        OrderService $orderService,
        UserService $userService
    ): JsonResponse {
        $user = $userService->findByIdOrFail($userId);
        $orders = $orderService->findByUser($user);

        return new JsonResponse($orderService->collectionToArray($orders));
    }
}
