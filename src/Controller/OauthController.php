<?php

namespace Cybalex\OauthServer\Controller;

use Cybalex\OauthServer\Event\PreTokenGrantEvent;
use Cybalex\OauthServer\Event\TokenGrantedEvent;
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
     * @param ObjectManager $objectManager
     * @param OAuth2 $server
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
     * @return Response
     */
    public function token(Request $request): Response
    {
        try {
            $event = new PreTokenGrantEvent($request);
            $this->eventDispatcher->dispatch($event);

            $response = $this->server->grantAccessToken($request);

            $successEvent = new TokenGrantedEvent($request, $response);

            /** @var TokenGrantedEvent $successEvent */
            $successEvent = $this->eventDispatcher->dispatch($successEvent);

            return $successEvent->getResponse();
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}
