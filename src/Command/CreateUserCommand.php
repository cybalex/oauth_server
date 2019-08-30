<?php

namespace App\Command;

use App\Services\UserManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'oauth-server:user:create';

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(ObjectManager $objectEntity, UserManager $userManager, string $name = null)
    {
        parent::__construct($name);

        $this->userManager = $userManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a new user.')
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'The username of the user.')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'The email of the user')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The username of the user.')
            ->setHelp('This command allows you to create a user...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);

        $this->userManager->createUser(
            $input->getOption('username'),
            $input->getOption('email'),
            $input->getOption('password')
        );

        $output->writeln('Username: '.$input->getOption('username'));
        $output->writeln('Email address: '.$input->getOption('email'));
        $output->writeln('Password: '.$input->getOption('password'));
        $output->writeln('');
        $output->writeln('User successfully generated!');
    }
}
