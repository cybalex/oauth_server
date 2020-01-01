<?php

namespace Cybalex\OauthServer\Tests\Controller;

use Cybalex\OauthServer\Controller\OauthController;
use Cybalex\OauthServer\Event\PreTokenGrantAccessEvent;
use Cybalex\OauthServer\Oauth2Events;
use Doctrine\Common\Persistence\ObjectManager;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function testToken()
    {
        $expectedEvent = new PreTokenGrantAccessEvent($this->request);
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(Oauth2Events::PRE_TOKEN_GRANT_ACCESS, $expectedEvent);

        $response = $this->createMock(Response::class);
        $this->oauth2
            ->expects($this->once())
            ->method('grantAccessToken')
            ->with($this->request)
            ->willReturn($response);

        $this->assertSame($response, $this->controller->token($this->request));
    }

    public function testOAuth2ServerException()
    {
        $expectedEvent = new PreTokenGrantAccessEvent($this->request);
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(Oauth2Events::PRE_TOKEN_GRANT_ACCESS, $expectedEvent);

        $expectedException = new OAuth2ServerException(Response::HTTP_BAD_REQUEST, 'error message');

        $this->oauth2->expects($this->once())->method('grantAccessToken')->with($this->request)
            ->willThrowException($expectedException);

        $response = $this->controller->token($this->request);

        $this->assertEquals('{"error":"error message","error_description":null}', $response->getContent());
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
