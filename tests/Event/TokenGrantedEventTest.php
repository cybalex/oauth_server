<?php

namespace App\Tests\Event;

use Cybalex\OauthServer\Event\TokenGrantedEvent;
use Cybalex\TestHelpers\GettersAndSettersTestTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenGrantedEventTest extends TestCase
{
    use GettersAndSettersTestTrait;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->response = $this->createMock(Response::class);
        $this->gettersAndSetterSetUp();

    }

    /**
     * @inheritDoc
     */
    function gettersAndSettersDataProvider(): array
    {
        $response = $this->getMockBuilder(Response::class)->getMock();

        return [
            ['response', $response],
        ];
    }

    function getEntityClass()
    {
        return TokenGrantedEvent::class;
    }

    protected function getConstructorArguments(): array
    {
        return [$this->request, $this->response];
    }

    public function testGetRequest()
    {
        $event = new TokenGrantedEvent($this->request, $this->response);

        $this->assertEquals($this->request, $event->getRequest());
    }
}
