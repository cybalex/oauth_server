<?php

namespace Cybalex\OauthServer\Services\ORM;

use Cybalex\OauthServer\Entity\ORM\User;
use Cybalex\OauthServer\Services\StringCanonicalizer;
use Cybalex\OauthServer\Services\UserManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager implements UserManagerInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var StringCanonicalizer
     */
    private $canonicalizer;

    /**
     * UserManager constructor.
     */
    public function __construct(
        ObjectManager $objectManager,
        PasswordEncoderInterface $passwordEncoder,
        StringCanonicalizer $canonicalizer
    ) {
        $this->objectManager = $objectManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->canonicalizer = $canonicalizer;
    }

    public function create(string $username, string $email, string $plainPassword, array $roles): void
    {
        $user = $this->getNewUserInstance();

        $canonicalizedEmail = $this->canonicalizer->canonicalize($email);
        $emailParts = explode('@', $canonicalizedEmail, 2);

        if ($emailParts[1] === 'gmail.com') {
            $canonicalizedEmailLogin = implode('', explode('.', $emailParts[0]));
            $canonicalizedEmail = implode('@', [$canonicalizedEmailLogin, $emailParts[1]]);
        }

        $user
            ->setUsername($this->canonicalizer->canonicalize($username))
            ->setEmail($canonicalizedEmail)
            ->setPassword($this->passwordEncoder->encodePassword($plainPassword, $user->getSalt()))
            ->setEnabled(true);

        $roles = array_map(function ($role) {
                return sprintf('ROLE_%s', strtoupper($role));
            }, $roles
        );

        $user->setRoles($roles);

        $this->objectManager->persist($user);
        $this->objectManager->flush();
    }

    public function update(UserInterface $user): void
    {
        $this->objectManager->persist($user);
        $this->objectManager->flush();
    }

    protected function getNewUserInstance(): User
    {
        return new User();
    }
}
