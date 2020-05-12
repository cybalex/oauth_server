<?php

namespace Cybalex\OauthServer\Command;

use Cybalex\OauthServer\Services\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'oauth-server:user:create';

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * CreateUserCommand constructor.
     * @param UserManagerInterface $userManager
     * @param string|null $name
     */
    public function __construct(UserManagerInterface $userManager, string $name = null)
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
            ->addOption('role', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The user roles.')
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

        $this->userManager->create(
            $input->getOption('username'),
            $input->getOption('email'),
            $input->getOption('password'),
            $input->getOption('role')
        );

        $output->writeln('Username: '.$input->getOption('username'));
        $output->writeln('Email address: '.$input->getOption('email'));
        $output->writeln('Password: '.$input->getOption('password'));
        $output->writeln('User roles: '.implode(' ', $input->getOption('role')));
        $output->writeln('');
        $output->writeln('User successfully generated!');
    }
}
