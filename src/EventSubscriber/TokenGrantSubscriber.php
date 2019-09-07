<?php

namespace Cybalex\OauthServer\EventSubscriber;

use Cybalex\OauthServer\Event\PreTokenGrantAccessEvent;
use Cybalex\OauthServer\Oauth2Events;
use Cybalex\OauthServer\Services\UserScopeAuthenticator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TokenGrantSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserScopeAuthenticator
     */
    private $userScopeAuthenticator;

    public function __construct(UserScopeAuthenticator $userScopeAuthenticator)
    {
        $this->userScopeAuthenticator = $userScopeAuthenticator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [Oauth2Events::PRE_TOKEN_GRANT_ACCESS => 'authenticate'];
    }

    /**
     * @param PreTokenGrantAccessEvent $event
     *
     * @throws \OAuth2\OAuth2ServerException
     */
    public function authenticate(PreTokenGrantAccessEvent $event): void
    {
        $request = $event->getRequest();
        $this->userScopeAuthenticator->authenticate($request);
    }
}
