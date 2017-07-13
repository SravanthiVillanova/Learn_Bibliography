<?php

namespace App\Action\Publisher;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;

class NewPublisherFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $adapter = $container->get(Adapter::class);
        //return new NewPublisherAction($router, $template, $adapter);
        return new \App\Action\SimpleRenderAction('app::publisher::new_publisher', $router, $template, $adapter);
    }
}
