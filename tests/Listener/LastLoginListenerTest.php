<?php

namespace Cybalex\OauthServer\Tests\Listener;

use Cybalex\OauthServer\Entity\ORM\User;
use Cybalex\OauthServer\Event\TokenGrantedEvent;
use Cybalex\OauthServer\Listener\LastLoginListener;
use Cybalex\OauthServer\Services\ORM\UserProvider;
use Cybalex\OauthServer\Services\UserManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class LastLoginListenerTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        $lastLoginListener = $this->getMockBuilder(LastLoginListener::class)->disableOriginalConstructor()
            ->getMock();
        $this->assertTrue(method_exists($lastLoginListener, 'updateLastLogin'));
        $this->assertEquals(
            [TokenGrantedEvent::class => ['updateLastLogin']],
            LastLoginListener::getSubscribedEvents()
        );
        $this->assertTrue(method_exists($lastLoginListener, 'updateLastLogin'));
    }

    /**
     * @throws \Exception
     */
    public function testUpdateLastLoginNoTokenInResponse(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getContent')->with()->willReturn(null);

        $event = $this->createMock(TokenGrantedEvent::class);
        $event->expects($this->once())->method('getResponse')->with()->willReturn($response);

        $userManager = $this->createMock(UserManagerInterface::class);

        $userProvider = $this->createMock(UserProvider::class);
        $userProvider->expects($this->never())->method('getUserByAccessToken');

        $listener = new LastLoginListener($userManager, $userProvider);
        $listener->updateLastLogin($event);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateLastLoginNoUser(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getContent')->with()
            ->willReturn(json_encode(['access_token' => 'token']));

        $event = $this->createMock(TokenGrantedEvent::class);
        $event->expects($this->once())->method('getResponse')->with()->willReturn($response);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->never())->method('update');

        $userProvider = $this->createMock(UserProvider::class);
        $userProvider->expects($this->once())->method('getUserByAccessToken')->with('token')
        ->willReturn(null);

        $listener = new LastLoginListener($userManager, $userProvider);
        $listener->updateLastLogin($event);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateLastLogin(): void
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('getContent')->with()
            ->willReturn(json_encode(['access_token' => 'token']));

        $event = $this->createMock(TokenGrantedEvent::class);
        $event->expects($this->once())->method('getResponse')->with()->willReturn($response);

        $user = $this->createMock(User::class);

        $expectedDate = new \DateTime();
        $user->expects($this->once())->method('setLastLogin')->with(
            $this->callback(
                function (\DateTime $date) use ($expectedDate) {
                    $this->assertEqualsWithDelta($date->getTimestamp(), $expectedDate->getTimestamp(), 5);

                    return true;
                }
            )
        )->willReturnSelf();

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('update')->with($user);

        $userProvider = $this->createMock(UserProvider::class);
        $userProvider->expects($this->once())->method('getUserByAccessToken')->with('token')
            ->willReturn($user);

        $listener = new LastLoginListener($userManager, $userProvider);
        $listener->updateLastLogin($event);
    }
}
