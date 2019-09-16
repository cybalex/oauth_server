<?php

namespace Cybalex\OauthServer\Tests\Event;

use Cybalex\OauthServer\Event\PreTokenGrantAccessEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PreTokenGrantAccessEventTest extends TestCase
{
    public function testGetRequest()
    {
        $request = $this->createMock(Request::class);
        $event = new PreTokenGrantAccessEvent($request);
        $this->assertSame($request, $event->getRequest());
    }
}
