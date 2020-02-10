<?php

namespace Cybalex\OauthServer\Services;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserManagerInterface
{
    public function create(string $username, string $email, string $plainPassword, array $roles): void;

    public function update(UserInterface $user): void;
}
