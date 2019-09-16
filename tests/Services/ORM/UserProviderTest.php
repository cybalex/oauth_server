<?php

namespace Cybalex\OauthServer\Tests\Services\ORM;

use Cybalex\OauthServer\Entity\ORM\User;
use Cybalex\OauthServer\Services\ORM\UserProvider;
use Cybalex\TestHelpers\ProtectedMethodsTestTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChild extends User
{
}

class DummyUser
{
}

class UserProviderTest extends TestCase
{
    use ProtectedMethodsTestTrait;

    /**
     * @var ObjectManager|MockObject
     */
    private $objectManager;

    /**
     * @var ObjectRepository|MockObject
     */
    private $objectRepository;

    /**
     * @var UserProvider
     */
    private $userProvider;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->objectRepository = $this->createMock(ObjectRepository::class);
    }

    /**
     * @return array
     */
    public function userClassProvider(): array
    {
        return [
            [User::class, true],
            [UserChild::class, true],
            [DummyUser::class, false],
        ];
    }

    /**
     * @dataProvider userClassProvider
     * @param string $className
     * @param bool $result
     */
    public function testSupportsClass(string $className, bool $result)
    {
        $this->objectManager->expects(static::once())->method('getRepository')->with(User::class)
            ->willReturn($this->objectRepository);
        $userProvider = new UserProvider($this->objectManager);

        $this->objectRepository->expects(static::once())->method('getClassName')->willReturn(User::class);
        $this->assertEquals($result, $userProvider->supportsClass($className));
    }

    public function testRefreshUserThrowsException()
    {
        $user = $this->createMock(UserInterface::class);
        $userProvider = $this->getMockBuilder(UserProvider::class)->setConstructorArgs([$this->objectManager])
            ->onlyMethods(['supportsClass'])
            ->getMock();

        $userProvider->expects(static::once())->method('supportsClass')->with(\get_class($user))->willReturn(false);

        $this->expectException(UnsupportedUserException::class);

        $userProvider->refreshUser($user);
    }

    public function testRefreshUser()
    {
        $user = $this->createMock(User::class);
        $user->expects(static::once())->method('getId')->with()->willReturn(134);
        $userProvider = $this->getMockBuilder(UserProvider::class)->setConstructorArgs([$this->objectManager])
            ->enableOriginalConstructor()
            ->onlyMethods(['supportsClass'])
            ->getMock();

        $userProvider->expects(static::once())->method('supportsClass')->with(\get_class($user))->willReturn(true);

        $this->objectManager->expects(static::once())->method('getRepository')->with(User::class)
            ->willReturn($this->objectRepository);
        $expectedUser = $this->createMock(UserInterface::class);
        $this->objectRepository->expects(static::once())->method('find')->with(134)->willReturn($expectedUser);

        $this->assertSame($expectedUser, $userProvider->refreshUser($user));
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetUserRepository()
    {
        $userProvider = new UserProvider($this->objectManager);
        $this->objectManager->expects(static::once())->method('getRepository')->with(User::class)
            ->willReturn($this->objectRepository);

        $this->assertSame(
            [$this->objectRepository, $this->objectRepository],
            $this->invokeMethodConsecutive($userProvider, 'getUserRepository', [[], []])
        );
    }
}
