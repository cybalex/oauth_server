<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OauthController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var OAuth2
     */
    private $server;


    /**
     * OauthController constructor.
     * @param ObjectManager $objectManager
     * @param OAuth2 $server
     */
    public function __construct(ObjectManager $objectManager, OAuth2 $server)
    {
        $this->objectManager = $objectManager;
        $this->server = $server;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function token(Request $request)
    {
        if (!$request->get('scope')) {
            throw new BadRequestHttpException('Empty or missing scope is provided in the requested');
        }

        try {
            return $this->server->grantAccessToken($request);
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}
