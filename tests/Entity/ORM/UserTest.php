<?php

namespace Cybalex\OauthServer\Tests\Entity\ORM;

use Cybalex\OauthServer\Entity\ORM\User;
use Cybalex\TestHelpers\GettersAndSettersTestTrait;
use DateTime;
use PHPUnit\Framework\TestCase;
use TypeError;

class UserTest extends TestCase
{
    use GettersAndSettersTestTrait;

    /**
     * @var User|object
     */
    protected $entity;

    /**
     * {@inheritdoc}
     */
    public function gettersAndSettersDataProvider(): array
    {
        return [
            ['password', 'p@$$w0rd'],
            ['username', 'John'],
            ['salt', null],
            ['salt', 'r2d2'],
            ['plainPassword', null],
            ['plainPassword', 'insecure'],
            ['enabled', true],
            ['email', 'test@domain.com'],
            ['email', null],
            ['roles', ['ROLE_USER']],
            ['lastLogin', null],
            ['lastLogin', new DateTime()],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return User::class;
    }

    public function testEraseCredentials()
    {
        $this->assertSame($this->entity, $this->entity->setPlainPassword('insecure'));
        $this->entity->eraseCredentials();
        $this->assertEmpty($this->entity->getPlainPassword());
    }

    public function testGetIdOnEmptyUser()
    {
        $this->expectException(TypeError::class);
        $this->entity->getId();
    }
}
