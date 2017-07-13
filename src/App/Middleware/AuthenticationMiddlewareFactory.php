<?php

namespace App\Middleware;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AuthenticationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;

        $basePath = $container->get(\Blast\BaseUrl\BasePathHelper::class)->__invoke();
        $session = $container->get(\Zend\Session\Container::class);

        return new AuthenticationMiddleware($router, $template, $basePath, $session);
    }
}
