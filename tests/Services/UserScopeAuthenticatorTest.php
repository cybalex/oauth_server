<?php

namespace Cybalex\OauthServer\Tests\Services;

use Cybalex\OauthServer\Services\UserScopeAuthenticator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use OAuth2\OAuth2ServerException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class UserScopeAuthenticatorTest extends TestCase
{
    /**
     * @var ObjectManager|MockObject
     */
    private $objectManager;

    /**
     * @var UserScopeAuthenticator
     */
    private $userScopeAuthenticator;

    /**
     * @var MockObject|Request
     */
    private $request;

    public function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->request = $this->createMock(Request::class);
        $this->userScopeAuthenticator = new UserScopeAuthenticator($this->objectManager);
    }

    /**
     * @throws \OAuth2\OAuth2ServerException
     */
    public function testAuthenticateNotSupports()
    {
        $this->request->expects(static::once())->method('get')->with('grant_type')->willReturn('client_credentials');

        $this->assertTrue($this->userScopeAuthenticator->authenticate($this->request));
    }

    /**
     * @throws \OAuth2\OAuth2ServerException
     */
    public function testAuthenticateSupportsWithCorrectScopes()
    {
        $this->request->expects(static::exactly(3))->method('get')
            ->withConsecutive(['grant_type'], ['scope'], ['username'])
            ->willReturnOnConsecutiveCalls('password', 'user', 'john');

        $user = $this->createMock(UserInterface::class);
        $user->expects(static::once())->method('getRoles')->with()->willReturn(['ROLE_USER', 'ROLE_ADMIN']);

        $userRepository = $this->createMock(ObjectRepository::class);
        $userRepository->expects(static::once())->method('findOneBy')->with(['username' => 'john'])->willReturn($user);

        $this->objectManager->expects(static::once())->method('getRepository')->with()->willReturn($userRepository);

        $this->assertTrue($this->userScopeAuthenticator->authenticate($this->request));
    }

    /**
     * @throws \OAuth2\OAuth2ServerException
     */
    public function testAuthenticateSupportsWithWrongScope()
    {
        $this->request->expects(static::exactly(3))->method('get')
            ->withConsecutive(['grant_type'], ['scope'], ['username'])
            ->willReturnOnConsecutiveCalls('password', 'admin', 'john');

        $user = $this->createMock(UserInterface::class);
        $user->expects(static::once())->method('getRoles')->with()->willReturn(['ROLE_USER', 'ROLE_EDITOR']);

        $userRepository = $this->createMock(ObjectRepository::class);
        $userRepository->expects(static::once())->method('findOneBy')->with(['username' => 'john'])->willReturn($user);

        $this->objectManager->expects(static::once())->method('getRepository')->with()->willReturn($userRepository);

        $this->expectException(OAuth2ServerException::class);

        $this->userScopeAuthenticator->authenticate($this->request);
    }
}
