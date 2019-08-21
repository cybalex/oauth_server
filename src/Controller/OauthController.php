<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\NativePasswordEncoder;

class OauthController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var OAuth2
     */
    private $server;

    public function __construct(ObjectManager $objectManager, OAuth2 $server)
    {
        $this->objectManager = $objectManager;
        $this->server = $server;
    }

    /**
     *
     * ToDo: This dummy method should not be here => create console command to create users instead
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createTestUser(Request $request)
    {
        try {
            $user = new User();
            $user->setUsername('test');
            $passwordEncoder = new NativePasswordEncoder();
            $user->setPassword($passwordEncoder->encodePassword('test', $user->getSalt()));
            $user->setPlainPassword('test');

            $this->objectManager->persist($user);
            $this->objectManager->flush();

        } catch (\Exception $e) {
            return new JsonResponse(['not ok']);
        }

        return new JsonResponse(['ok']);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function token(Request $request)
    {
        if (!$request->get('scope')) {
            throw new BadRequestHttpException('Empty or missing scope is provided in the requested');
        }

        try {
            return $this->server->grantAccessToken($request);
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}
