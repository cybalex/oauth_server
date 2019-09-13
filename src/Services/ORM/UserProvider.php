<?php

namespace Cybalex\OauthServer\Services\ORM;

use Cybalex\OauthServer\Entity\ORM\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var ObjectRepository
     */
    private $userRepository;

    public function __construct(ObjectManager $objectManager)
    {
        $this->userRepository = $objectManager->getRepository(User::class);
    }

    /**
     * @param string $username
     *
     * @return mixed|UserInterface
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $q = $this->userRepository
            ->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery();

        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin AcmeDemoBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     *
     * @return object|UserInterface|null
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        /* @var User $user */
        return $this->userRepository->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->userRepository->getClassName() === $class
            || is_subclass_of($class, $this->userRepository->getClassName());
    }
}
