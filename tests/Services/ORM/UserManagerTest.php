<?php

declare(strict_types=1);

namespace Cybalex\OauthServer\Tests\Services\ORM;

use Cybalex\OauthServer\Entity\ORM\User;
use Cybalex\OauthServer\Services\ORM\UserManager;
use Cybalex\OauthServer\Services\StringCanonicalizer;
use Cybalex\TestHelpers\ProtectedMethodsTestTrait;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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

    /**
     * @var StringCanonicalizer|MockObject
     */
    private $canonicalizer;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->passwordEncoder = $this->createMock(PasswordEncoderInterface::class);
        $this->canonicalizer = $this->createMock(StringCanonicalizer::class);
    }

    public function testCreateUser()
    {
        /** @var UserManager|MockObject $userManager */
        $userManager = $this->getMockBuilder(UserManager::class)
            ->setConstructorArgs([
                $this->objectManager,
                $this->passwordEncoder,
                $this->canonicalizer,
            ])
            ->onlyMethods(['getNewUserInstance'])
            ->getMock();

        $username = 'John';
        $usernameCanonical = 'JohnCanonical';
        $email = 'john@mail.com';
        $emailCanonical = 'johndoe@gmail.com';
        $plainPassword = 'insecure';
        $encodedPassword = 'encodedPassword';
        $salt = 'salt';

        $this->canonicalizer
            ->expects(static::exactly(2))
            ->method('canonicalize')
            ->withConsecutive([$email], ['John'])
            ->willReturnOnConsecutiveCalls('johndoe@gmail.com', 'JohnCanonical');

        $user = $this->createMock(User::class);
        $user->expects(static::once())->method('setUsername')->with($usernameCanonical)->willReturnSelf();
        $user->expects(static::once())->method('setEmail')->with($emailCanonical)->willReturnSelf();
        $user->expects(static::once())->method('setPassword')->with($encodedPassword)->willReturnSelf();
        $user->expects(static::once())->method('getSalt')->with()->willReturn($salt);
        $user->expects(static::once())->method('setRoles')->with(['ROLE_USER', 'ROLE_ADMIN'])->willReturnSelf();
        $user->expects(static::once())->method('setEnabled')->with(true)->willReturnSelf();

        $this->passwordEncoder->expects(static::once())->method('encodePassword')->with($plainPassword, $salt)
            ->willReturn($encodedPassword);

        $userManager->expects(static::once())->method('getNewUserInstance')->with()->willReturn($user);

        $this->objectManager->expects(static::once())->method('persist')->with($user);
        $this->objectManager->expects(static::once())->method('flush')->with();

        $userManager->create($username, $email, $plainPassword, ['user', 'admin']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetNewUserInstance()
    {
        $userManager = new UserManager(
            $this->objectManager,
            $this->passwordEncoder,
            $this->canonicalizer
        );

        /** @var User $actualUser */
        $actualUser = $this->invokeMethod($userManager, 'getNewUserInstance', []);
        $actualUser->setSalt(null);

        $expectedUser = new User();
        $expectedUser->setSalt(null);

        $this->assertEquals($actualUser, $expectedUser);
    }

    public function testUpdate()
    {
        $user = $this->createMock(UserInterface::class);

        $userManager = new UserManager(
            $this->objectManager,
            $this->passwordEncoder,
            $this->canonicalizer
        );

        $this->objectManager->expects($this->once())->method('persist')->with($user);
        $this->objectManager->expects($this->once())->method('flush')->with();

        $userManager->update($user);

    }
}
