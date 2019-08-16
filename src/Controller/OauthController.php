<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class OauthController extends AbstractController
{
    public function oauth()
    {
        return new JsonResponse(['hello' => 'world']);
    }
}
