<?php
namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    public function create(
        Request $request,
        UserService $userService
    ): JsonResponse {
        $user = $userService->create(
            $request->get('full_name'),
            $request->get('address'),
            $request->get('country'),
            $request->get('city'),
            $request->get('phone'),
            10000,
            $request->get('zip'),
            $request->get('state')
        );

        return new JsonResponse($userService->toArray($user));
    }
}
