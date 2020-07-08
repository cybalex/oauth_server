<?php

namespace Cybalex\OauthServer\Tests\Listener;

use Cybalex\OauthServer\DTO\CorsConfig;
use Cybalex\OauthServer\Listener\CorsListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class CorsListenerTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        $corsConfig = $this->createMock(CorsConfig::class);
        $listener = new CorsListener($corsConfig);

        $this->assertTrue(method_exists($listener, 'onKernelException'));
        $this->assertTrue(method_exists($listener, 'onKernelResponse'));

        $expectedSubscribedEvents = [
            KernelEvents::RESPONSE => ['onKernelResponse', 9999],
            KernelEvents::EXCEPTION => ['onKernelException', 9999],
        ];
        $this->assertSame($expectedSubscribedEvents, CorsListener::getSubscribedEvents());
    }

    public function testOnKernelResponseIsMasterRequest(): void
    {
        $corsConfig = $this->createMock(CorsConfig::class);
        $listener = new CorsListener($corsConfig);
        $event = $this->createMock(ResponseEvent::class);
        $event->expects($this->once())->method('isMasterRequest')->with()->willReturn(false);
        $event->expects($this->never())->method('getResponse')->with();

        $listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithResponse(): void
    {
        $corsConfig = $this->createMock(CorsConfig::class);
        $this->setupCorsConfigExpectations($corsConfig);

        $listener = new CorsListener($corsConfig);
        $event = $this->createMock(ResponseEvent::class);
        $event->expects($this->once())->method('isMasterRequest')->with()->willReturn(true);
        $response = $this->createMock(Response::class);
        $headers = $this->createMock(ResponseHeaderBag::class);
        $this->setRequestHeaderBagExpectations($headers);

        $response->headers = $headers;
        $event->expects($this->once())->method('getResponse')->with()->willReturn($response);

        $listener->onKernelResponse($event);
    }

    public function testOnKernelException(): void
    {
        $corsConfig = $this->createMock(CorsConfig::class);
        $this->setupCorsConfigExpectations($corsConfig);

        $listener = new CorsListener($corsConfig);
        $event = $this->createMock(ExceptionEvent::class);
        $response = $this->createMock(Response::class);
        $headers = $this->createMock(ResponseHeaderBag::class);
        $this->setRequestHeaderBagExpectations($headers);

        $response->headers = $headers;
        $event->expects($this->once())->method('getResponse')->with()->willReturn($response);

        $listener->onKernelException($event);
    }

    /**
     * @param MockObject $corsConfig
     */
    private function setupCorsConfigExpectations(MockObject  $corsConfig): void
    {
        $corsConfig->expects($this->once())->method('getAllowHeaders')->with()->willReturn('Content-Type');
        $corsConfig->expects($this->once())->method('getAllowMethods')->with()->willReturn('GET,POST');
        $corsConfig->expects($this->once())->method('getAllowOrigin')->with()->willReturn('example.com');
    }

    /**
     * @param MockObject $headers
     */
    private function setRequestHeaderBagExpectations(MockObject $headers): void
    {
        $headers
            ->expects($this->exactly(3))
            ->method('set')
            ->withConsecutive(
                ['Access-Control-Allow-Origin', 'example.com'],
                ['Access-Control-Allow-Methods', 'GET,POST'],
                ['Access-Control-Allow-Headers', 'Content-Type']
            );
    }
}
