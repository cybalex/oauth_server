<?php

namespace Cybalex\OauthServer\Tests\Controller;

use Cybalex\OauthServer\Controller\OauthController;
use Cybalex\OauthServer\Services\UserScopeAuthenticator;
use Doctrine\Common\Persistence\ObjectManager;
use OAuth2\OAuth2;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class OauthControllerTest extends TestCase
{
    /**
     * @var ObjectManager|MockObject
     */
    private $objectManager;

    /**
     * @var OAuth2|MockObject
     */
    private $oauth2;

    /**
     * @var MockObject|Request
     */
    private $request;

    /**
     * @var EventDispatcher|MockObject
     */
    private $eventDispatcher;

    /**
     * @var OauthController
     */
    private $controller;

    public function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->oauth2 = $this->createMock(OAuth2::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->controller = new OauthController($this->objectManager, $this->oauth2, $this->eventDispatcher);
        $this->request = $this->createMock(Request::class);
    }

    public function testTokenWithoutScopes()
    {
        $this->request->expects(static::once())->method('get')->with('scope')->willReturn(null);

        $expectedResponseText = '{"error":400,"error_description":"Empty or missing scope is provided in the requested"}';
        $expectedResponseStatusCode = 400;
        $response = $this->controller->token($this->request);

        $this->assertEquals($expectedResponseText, $response->getContent());
        $this->assertEquals($expectedResponseStatusCode, $response->getStatusCode());
    }
}
