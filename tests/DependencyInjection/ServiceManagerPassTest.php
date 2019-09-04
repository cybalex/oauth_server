<?php

namespace Cybalex\OauthServer\Tests\DependencyInjection;

use Cybalex\OauthServer\Controller\OauthController;
use Cybalex\OauthServer\DependencyInjection\ServiceManagerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ServiceManagerPassTest extends TestCase
{
    public function testProcess()
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);
        $containerBuilder
            ->expects(static::exactly(2))
            ->method('removeDefinition')
            ->withConsecutive(['fos_oauth_server.authorize.form'], ['fos_oauth_server.controller.authorize']);

        $containerBuilder
            ->expects(static::once())
            ->method('register')
            ->with('fos_oauth_server.controller.token', OauthController::class);

        $serviceManagerPass = new ServiceManagerPass();

        $serviceManagerPass->process($containerBuilder);
    }
}
