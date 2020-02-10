<?php

namespace Cybalex\OauthServer\Tests\Services\ORM;

use Cybalex\OauthServer\Entity\ORM\AccessToken;
use Cybalex\OauthServer\Entity\ORM\User;
use Cybalex\OauthServer\Services\ORM\UserProvider;
use Cybalex\TestHelpers\ProtectedMethodsTestTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
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
     */
    public function testSupportsClass(string $className, bool $result)
    {
        $this->objectManager->expects(static::once())->method('getRepository')->with(User::class)
            ->willReturn($this->objectRepository);
        $userProvider = new UserProvider($this->objectManager);

        $this->objectRepository->expects(static::once())->method('getClassName')->willReturn(User::class);
        $this->assertSame($result, $userProvider->supportsClass($className));
    }

    public function testRefreshUserThrowsException()
    {
        $user = $this->createMock(UserInterface::class);
        /** @var UserProvider|MockObject $userProvider */
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
        /** @var UserProvider|MockObject $userProvider */
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

    /**
     * @throws NonUniqueResultException
     */
    public function testLoadUserByUsernameThrowsException()
    {
        $username = 'Dart Vader';
        $exception = new NoResultException();

        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionMessage(
            'Unable to find an active admin AcmeDemoBundle:User object identified by "Dart Vader".'
        );

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())->method('getSingleResult')->with()->willThrowException($exception);

        $queryBuilder = $this
            ->getMockBuilder(QueryBuilder::class)
            ->onlyMethods(['where', 'setParameter', 'getQuery'])
            ->disableOriginalConstructor()
            ->getMock();
        $queryBuilder
            ->expects($this->once())
            ->method('where')
            ->with('u.username = :username OR u.email = :email')
            ->willReturnSelf();
        $queryBuilder
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(['username', $username], ['email', $username])
            ->willReturnSelf();
        $queryBuilder->expects($this->once())->method('getQuery')->with()->willReturn($query);

        $objectRepository = $this->createMock(EntityRepository::class);

        $objectRepository->expects($this->once())->method('createQueryBuilder')->with('u')
            ->willReturn($queryBuilder);

        $this->objectManager->expects($this->once())->method('getRepository')->with(User::class)
            ->willReturn($objectRepository);

        $userProvider = new UserProvider($this->objectManager);

        $userProvider->loadUserByUsername($username);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testLoadUserByUsernameWithoutException()
    {
        $username = 'John Doe';

        $user = $this->createMock(UserInterface::class);

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())->method('getSingleResult')->with()->willReturn($user);

        $queryBuilder = $this
            ->getMockBuilder(QueryBuilder::class)
            ->onlyMethods(['where', 'setParameter', 'getQuery'])
            ->disableOriginalConstructor()
            ->getMock();
        $queryBuilder
            ->expects($this->once())
            ->method('where')
            ->with('u.username = :username OR u.email = :email')
            ->willReturnSelf();
        $queryBuilder
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(['username', $username], ['email', $username])
            ->willReturnSelf();
        $queryBuilder->expects($this->once())->method('getQuery')->with()->willReturn($query);

        $objectRepository = $this->createMock(EntityRepository::class);

        $objectRepository->expects($this->once())->method('createQueryBuilder')->with('u')
            ->willReturn($queryBuilder);

        $this->objectManager->expects($this->once())->method('getRepository')->with(User::class)
            ->willReturn($objectRepository);

        $userProvider = new UserProvider($this->objectManager);

        $this->assertEquals($user, $userProvider->loadUserByUsername($username));
    }

    public function userProvider()
    {
        $user = $this->createMock(User::class);
        $token = $this->createMock(AccessToken::class);

        return [
            [$token, $user],
            [null, null],
        ];
    }

    /**
     * @dataProvider userProvider
     * @param AccessToken|MockObject|null $accessToken
     * @param UserInterface|MockObject|null $result
     */
    public function testGetUserByAccessToken($accessToken, ?UserInterface $result)
    {
        $token = 'NTU2YjdmZGM2NmRiMjY2YzRkZjVmMDgyZDhmZDBiNjg1Zj';

        if ($accessToken) {
            $accessToken->expects($this->once())->method('getUser')->with()->willReturn($result);
        }

        $objectRepository = $this->createMock(ObjectRepository::class);
        $objectRepository->expects($this->once())->method('findOneBy')->with(['token' => $token])
            ->willReturn($accessToken);

        $this->objectManager->expects($this->once())->method('getRepository')->with(AccessToken::class)
            ->willReturn($objectRepository);

        $userProvider = new UserProvider($this->objectManager);
        $this->assertEquals($result, $userProvider->getUserByAccessToken($token));
    }
}
