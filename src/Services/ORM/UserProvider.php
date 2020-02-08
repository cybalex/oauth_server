<?php

namespace Cybalex\OauthServer\Services\ORM;

use Cybalex\OauthServer\Entity\ORM\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var ObjectRepository
     */
    private $objectManager;

    /**
     * @var ObjectRepository|null
     */
    private $userRepository = null;

    /**
     * UserProvider constructor.
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $username
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($username): UserInterface
    {
        /** @var QueryBuilder $qb */
        $qb = $query = $this->getUserRepository()
            ->createQueryBuilder('u');

        /** @var Query $query */
        $query = $qb
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery();

        try {
            $user = $query->getSingleResult();
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
     * @param UserInterface|User $user
     *
     * @return UserInterface|object|null
     */
    public function refreshUser(UserInterface $user)
    {
        $class = \get_class($user);

        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->getUserRepository()->find($user->getId());
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        $repositoryClassName = $this->getUserRepository()->getClassName();

        return $repositoryClassName === $class || \is_subclass_of($class, $repositoryClassName);
    }

    protected function getUserRepository(): ObjectRepository
    {
        if (!$this->userRepository) {
            $this->userRepository = $this->objectManager->getRepository(User::class);
        }

        return $this->userRepository;
    }
}
