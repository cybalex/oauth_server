<?php

namespace Cybalex\OauthServer\DependencyInjection;

use Cybalex\OauthServer\Controller\OauthController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ServiceManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // remove declarations of Fos oauth server services
        $container->removeDefinition('fos_oauth_server.authorize.form');
        $container->removeDefinition('fos_oauth_server.controller.authorize');

        // override declarations of Fos oauth server token controller
        $container->register('fos_oauth_server.controller.token', OauthController::class);
    }
}
