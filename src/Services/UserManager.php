<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\NativePasswordEncoder;

class UserManager
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var NativePasswordEncoder
     */
    private $passwordEncoder;

    /**
     * UserManager constructor.
     *
     * @param ObjectManager         $objectManager
     * @param NativePasswordEncoder $passwordEncoder
     */
    public function __construct(ObjectManager $objectManager, NativePasswordEncoder $passwordEncoder)
    {
        $this->objectManager = $objectManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $plainPassword
     */
    public function createUser(string $username, string $email, string $plainPassword)
    {
        $user = $this->getNewUserInstance();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword($plainPassword, $user->getSalt()));

        $this->objectManager->persist($user);
        $this->objectManager->flush();
    }

    protected function getNewUserInstance(): User
    {
        return new User();
    }
}
