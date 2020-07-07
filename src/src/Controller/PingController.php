<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PingController extends AbstractController
{
    /**
     * @Route("/v1/ping", methods={"GET"})
     * @return JsonResponse
     */
    public function ping(): JsonResponse
    {
        return new JsonResponse([
            'data' => 'pong',
        ]);
    }
}
