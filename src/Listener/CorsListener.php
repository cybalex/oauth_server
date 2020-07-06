<?php

namespace Cybalex\OauthServer\Listener;

use Cybalex\OauthServer\DTO\CorsConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CorsListener implements EventSubscriberInterface
{
    /**
     * @var CorsConfig
     */
    private $corsConfig;

    /**
     * CorsListener constructor.
     * @param CorsConfig $config
     */
    public function __construct(CorsConfig $config)
    {
        $this->corsConfig = $config;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', 9999],
            KernelEvents::EXCEPTION => ['onKernelException', 9999],
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $response = $event->getResponse();

        if ($response) {
            $this->setHeaders($response);
        }
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();

        if ($response) {
            $this->setHeaders($response);
        }
    }

    /**
     * @param Response $response
     */
    private function setHeaders(Response $response)
    {
        $response->headers->set('Access-Control-Allow-Origin', $this->corsConfig->getAllowOrigin());
        $response->headers->set('Access-Control-Allow-Methods', $this->corsConfig->getAllowMethods());
        $response->headers->set('Access-Control-Allow-Headers', $this->corsConfig->getAllowHeaders());
    }
}
