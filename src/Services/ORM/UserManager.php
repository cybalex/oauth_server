<?php

namespace Cybalex\OauthServer\Services\ORM;

use Cybalex\OauthServer\Entity\ORM\User;
use Cybalex\OauthServer\Services\StringCanonicalizer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UserManager
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
     *
     * @param ObjectManager            $objectManager
     * @param PasswordEncoderInterface $passwordEncoder
     * @param StringCanonicalizer      $canonicalizer
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

    /**
     * @param string $username
     * @param string $email
     * @param string $plainPassword
     * @param array  $roles
     */
    public function createUser(string $username, string $email, string $plainPassword, array $roles)
    {
        $user = $this->getNewUserInstance();
        $user
            ->setUsername($username)
            ->setUsernameCanonical($this->canonicalizer->canonicalize($username))
            ->setEmail($email)
            ->setEmailCanonical($this->canonicalizer->canonicalize($email))
            ->setPassword($this->passwordEncoder->encodePassword($plainPassword, $user->getSalt()))
            ->setEnabled(true);

        $roles = \array_map(function ($role) {
            return sprintf('ROLE_%s', strtoupper($role));
        }, $roles
        );

        $user->setRoles($roles);

        $this->objectManager->persist($user);
        $this->objectManager->flush();
    }

    protected function getNewUserInstance(): User
    {
        return new User();
    }
}
