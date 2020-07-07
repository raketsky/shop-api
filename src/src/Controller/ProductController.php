<?php
namespace App\Controller;

use App\Exception\AppException;
use App\Service\PriceService;
use App\Service\ProductService;
use App\Service\UserService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    /**
     * @param Request        $request
     * @param ProductService $productService
     * @param UserService    $userService
     * @param PriceService   $priceService
     * @return JsonResponse
     * @throws AppException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(
        Request $request,
        ProductService $productService,
        UserService $userService,
        PriceService $priceService
    ): JsonResponse {
        $price = $priceService->toInt($request->get('price'));
        $product = $productService->create(
            $userService->findByIdOrFail($request->get('user_id')),
            $request->get('title'),
            $request->get('type'),
            $price,
            $request->get('stock')
        );

        return new JsonResponse($productService->toArray($product));
    }

    /**
     * @param int            $userId
     * @param UserService    $userService
     * @param ProductService $productService
     * @return JsonResponse
     * @throws AppException
     */
    public function listByUser(
        int $userId,
        UserService $userService,
        ProductService $productService
    ): JsonResponse {
        $user = $userService->findByIdOrFail($userId);
        $products = $productService->findByUser($user);

        return new JsonResponse($productService->collectionToArray($products));
    }
}
