<?php

namespace Cybalex\OauthServer\Controller;

use Cybalex\OauthServer\Event\PreTokenGrantAccessEvent;
use Cybalex\OauthServer\Oauth2Events;
use Doctrine\Common\Persistence\ObjectManager;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * OauthController constructor.
     */
    public function __construct(
        ObjectManager $objectManager,
        OAuth2 $server,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->objectManager = $objectManager;
        $this->server = $server;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return Response
     */
    public function token(Request $request)
    {
        try {
            $event = new PreTokenGrantAccessEvent($request);
            $this->eventDispatcher->dispatch(Oauth2Events::PRE_TOKEN_GRANT_ACCESS, $event);

            return $this->server->grantAccessToken($request);
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}
