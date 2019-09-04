<?php

namespace Cybalex\OauthServer\Controller;

use Cybalex\OauthServer\Services\UserScopeAuthenticator;
use Doctrine\Common\Persistence\ObjectManager;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @var UserScopeAuthenticator
     */
    private $userScopeAuthenticator;

    /**
     * OauthController constructor.
     *
     * @param ObjectManager          $objectManager
     * @param OAuth2                 $server
     * @param UserScopeAuthenticator $userScopeAuthenticator
     */
    public function __construct(
        ObjectManager $objectManager,
        OAuth2 $server,
        UserScopeAuthenticator $userScopeAuthenticator
    ) {
        $this->objectManager = $objectManager;
        $this->server = $server;
        $this->userScopeAuthenticator = $userScopeAuthenticator;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function token(Request $request)
    {
        if (!$request->get('scope')) {
            return (new OAuth2ServerException(400, 400, 'Empty or missing scope is provided in the requested'))
                ->getHttpResponse();
        }

        try {
            $this->userScopeAuthenticator->authenticate($request);

            return $this->server->grantAccessToken($request);
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}
