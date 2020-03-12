<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        return new JsonResponse(['grettings' => 'Hello World!']);
    }
}
