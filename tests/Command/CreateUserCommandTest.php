<?php

namespace Cybalex\OauthServer\Tests\Command;

use Cybalex\OauthServer\Command\CreateUserCommand;
use Cybalex\OauthServer\Services\ORM\UserManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends TestCase
{
    public function testExecute()
    {
        $userManager = $this->createMock(UserManager::class);

        $userManager->expects(static::once())->method('createUser')
            ->with('john', 'john.smith@mail.com', 'p@ssw0rd', ['user', 'admin']);

        $expectedOutput = <<<TEXT
User Creator
============

Username: john
Email address: john.smith@mail.com
Password: p@ssw0rd
User roles: user admin

User successfully generated!

TEXT;

        $command = new CreateUserCommand($userManager);

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                '--username' => 'john',
                '--email' => 'john.smith@mail.com',
                '--password' => 'p@ssw0rd',
                '--role' => ['user', 'admin'],
            ]
        );

        $this->assertSame($expectedOutput, $commandTester->getDisplay());
    }
}
