<?php

namespace Cybalex\OauthServer\Services\ORM;

use Cybalex\OauthServer\Entity\ORM\User;
use Cybalex\OauthServer\Exception\UnsupportedUserScopeException;
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
     * @var array
     */
    private $supportedScopesArray;

    /**
     * @var StringCanonicalizer
     */
    private $canonicalizer;

    /**
     * UserManager constructor.
     *
     * @param ObjectManager            $objectManager
     * @param PasswordEncoderInterface $passwordEncoder
     * @param string                   $supportedScopes
     * @param StringCanonicalizer      $canonicalizer
     */
    public function __construct(
        ObjectManager $objectManager,
        PasswordEncoderInterface $passwordEncoder,
        string $supportedScopes,
        StringCanonicalizer $canonicalizer
    ) {
        $this->objectManager = $objectManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->supportedScopesArray = explode(' ', $supportedScopes);
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $plainPassword
     * @param array  $roles
     *
     * @throws UnsupportedUserScopeException
     */
    public function createUser(string $username, string $email, string $plainPassword, array $roles)
    {
        $user = $this->getNewUserInstance();
        $user
            ->setUsername($username)
            ->setUsernameCanonical($this->canonicalizer->canonicalize($username))
            ->setEmail($email)
            ->setEmailCanonical($this->canonicalizer->canonicalize($email))
            ->setPassword($this->passwordEncoder->encodePassword($plainPassword, $user->getSalt()));

        $e = new UnsupportedUserScopeException();

        foreach ($roles as $requestedScope) {
            if (!\in_array($requestedScope, $this->supportedScopesArray, true)) {
                $e->addUnsupportedScope($requestedScope);
            }
        }

        if (!empty($e->getUnsupportedScopes())) {
            throw $e;
        }

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
