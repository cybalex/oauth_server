<?php

namespace App\Tests\EventSubscriber;

use Cybalex\OauthServer\Event\PreTokenGrantAccessEvent;
use Cybalex\OauthServer\EventSubscriber\TokenGrantSubscriber;
use Cybalex\OauthServer\Oauth2Events;
use Cybalex\OauthServer\Services\UserScopeAuthenticator;
use OAuth2\OAuth2ServerException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class TokenGrantSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents()
    {
        $this->assertSame(
            [Oauth2Events::PRE_TOKEN_GRANT_ACCESS => 'authenticate'],
            TokenGrantSubscriber::getSubscribedEvents()
        );
    }

    /**
     * @throws OAuth2ServerException
     */
    public function testAuthorize()
    {
        $request = $this->createMock(Request::class);
        $userScopeAuthenticator = $this->createMock(UserScopeAuthenticator::class);
        $userScopeAuthenticator->expects(static::once())->method('authenticate')->with($request)->willReturn(true);
        $event = $this->createMock(PreTokenGrantAccessEvent::class);
        $event->expects(static::once())->method('getRequest')->with()->willReturn($request);
        $tokenGrantSubscriber = new TokenGrantSubscriber($userScopeAuthenticator);

        $tokenGrantSubscriber->authenticate($event);
    }
}
