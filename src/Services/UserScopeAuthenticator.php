<?php

namespace Cybalex\OauthServer\Services;

use Cybalex\OauthEntities\Entity\ORM\User;
use Doctrine\Common\Persistence\ObjectManager;
use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Request;

class UserScopeAuthenticator
{
    private $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param Request $request
     *
     * @return bool
     *
     * @throws OAuth2ServerException
     */
    public function authenticate(Request $request)
    {
        if (!$this->supports($request)) {
            return true;
        }

        $scopesString = $request->get('scope');
        $scopesArray = explode(' ', $scopesString);

        $username = $request->get('username');
        /** @var User $user */
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['username' => $username]);

        foreach ($scopesArray as $scope) {
            $role = sprintf('ROLE_%s', strtoupper($scope));

            if (!in_array($role, $user->getRoles())) {
                throw new OAuth2ServerException(401, 401, 'Invalid or missing scopes provider in the request');
            }
        }

        return true;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function supports(Request $request): bool
    {
        if ('password' === $request->get('grant_type')) {
            return true;
        }

        return false;
    }
}
