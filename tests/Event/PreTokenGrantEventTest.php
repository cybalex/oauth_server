<?php

namespace Cybalex\OauthServer\Tests\Event;

use Cybalex\OauthServer\Event\PreTokenGrantEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PreTokenGrantAccessEventTest extends TestCase
{
    public function testGetRequest()
    {
        $request = $this->createMock(Request::class);
        $event = new PreTokenGrantEvent($request);
        $this->assertSame($request, $event->getRequest());
    }
}
