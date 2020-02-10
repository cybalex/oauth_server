<?php

namespace Cybalex\OauthServer\Services;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserManagerInterface
{
    /**
     * @param string $username
     * @param string $email
     * @param string $plainPassword
     * @param array $roles
     */
    public function create(string $username, string $email, string $plainPassword, array $roles): void;

    /**
     * @param UserInterface $user
     */
    public function update(UserInterface $user): void;
}
