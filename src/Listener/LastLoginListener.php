<?php

namespace Cybalex\OauthServer\Listener;

use Cybalex\OauthServer\Event\TokenGrantedEvent;
use Cybalex\OauthServer\Services\ORM\UserProvider;
use Cybalex\OauthServer\Services\UserManagerInterface;
use DateTime;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LastLoginListener implements EventSubscriberInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * LastLoginListener constructor.
     * @param UserManagerInterface $userManager
     * @param UserProvider $userProvider
     */
    public function __construct(UserManagerInterface $userManager, UserProvider $userProvider)
    {
        $this->userManager = $userManager;
        $this->userProvider = $userProvider;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [TokenGrantedEvent::class => ['updateLastLogin']];
    }

    /**
     * @param TokenGrantedEvent $event
     * @throws Exception
     */
    public function updateLastLogin(TokenGrantedEvent $event): void
    {
        $responseBody = json_decode($event->getResponse()->getContent(), true);

        if (\is_array($responseBody) && isset($responseBody['access_token'])) {
            $user = $this->userProvider->getUserByAccessToken($responseBody['access_token']);

            if (!$user && !method_exists($user, 'setLastLogin')) {
                return;
            }

            $user->setLastLogin(new DateTime());
            $this->userManager->update($user);
        }
    }
}
