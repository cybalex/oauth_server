<?php

namespace Cybalex\OauthServer\Tests\Services\ORM;;

use Cybalex\OauthServer\Entity\ORM\User;
use Cybalex\OauthServer\Services\ORM\UserManager;
use Cybalex\TestHelpers\ProtectedMethodsTestTrait;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UserManagerTest extends TestCase
{
    use ProtectedMethodsTestTrait;

    /**
     * @var PasswordEncoderInterface|MockObject
     */
    private $passwordEncoder;

    /**
     * @var ObjectManager|MockObject
     */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->passwordEncoder = $this->createMock(PasswordEncoderInterface::class);
    }

    public function testCreateUser()
    {
        $supportedScopes = 'user admin';

        /** @var UserManager|MockObject $userManager */
        $userManager = $this->getMockBuilder(UserManager::class)
            ->setConstructorArgs([
                $this->objectManager,
                $this->passwordEncoder,
                $supportedScopes
            ])
            ->onlyMethods(['getNewUserInstance'])
            ->getMock();

        $username = 'John';
        $email = 'john@mail.com';
        $plainPassword = 'insecure';
        $encodedPassword = 'encodedPassword';
        $salt = 'salt';

        $user = $this->createMock(User::class);
        $user->expects(static::once())->method('setUsername')->with($username)->willReturnSelf();
        $user->expects(static::once())->method('setEmail')->with($email)->willReturnSelf();
        $user->expects(static::once())->method('setPassword')->with($encodedPassword)->willReturnSelf();
        $user->expects(static::once())->method('getSalt')->with()->willReturn($salt);
        $user->expects(static::once())->method('setRoles')->with(['ROLE_USER', 'ROLE_ADMIN'])->willReturnSelf();

        $this->passwordEncoder->expects(static::once())->method('encodePassword')->with($plainPassword, $salt)
            ->willReturn($encodedPassword);

        $userManager->expects(static::once())->method('getNewUserInstance')->with()->willReturn($user);

        $this->objectManager->expects(static::once())->method('persist')->with($user);
        $this->objectManager->expects(static::once())->method('flush')->with();

        $userManager->createUser($username, $email, $plainPassword, ['user', 'admin']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetNewUserInstance()
    {
        $supportedScopes = 'user admin';
        $userManager = new UserManager($this->objectManager, $this->passwordEncoder, $supportedScopes);

        /** @var User $actualUser */
        $actualUser = $this->invokeMethod($userManager, 'getNewUserInstance', []);
        $actualUser->setSalt(null);

        $expectedUser = new User();
        $expectedUser->setSalt(null);

        $this->assertEquals($actualUser, $expectedUser);
    }
}
