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
     *
     * @param ObjectManager            $objectManager
     * @param OAuth2                   $server
     * @param EventDispatcherInterface $eventDispatcher
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
            $event = new PreTokenGrantAccessEvent($request);
            $this->eventDispatcher->dispatch(Oauth2Events::PRE_TOKEN_GRANT_ACCESS, $event);

            return $this->server->grantAccessToken($request);
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}
