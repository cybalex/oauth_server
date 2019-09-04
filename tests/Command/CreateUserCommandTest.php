<?php

namespace Cybalex\OauthServer\Tests\Command;

use Cybalex\OauthEntities\Services\ORM\UserManager;
use Cybalex\OauthServer\Command\CreateUserCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends TestCase
{
    /**
     * @var CreateUserCommand|MockObject
     */
    private $command;

    public function testExecute()
    {
        $userManager = $this->createMock(UserManager::class);
        $expectedOutput = <<<TEXT
User Creator
============

Username: john
Email address: john.smith@mail.com
Password: p@ssw0rd

User successfully generated!

TEXT;

        $command = new CreateUserCommand($userManager);

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            ['--username' => 'john', '--email' => 'john.smith@mail.com', '--password' => 'p@ssw0rd']
        );

        $this->assertSame($expectedOutput, $commandTester->getDisplay());
    }
}
