<?php

namespace App\Action\Publisher;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;

class AddPublisherLocationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $adapter = $container->get(Adapter::class);
        //return new AddPublisherLocationAction($router, $template, $adapter);
        return new \App\Action\SimpleRenderAction('app::publisher::add_publisher_location', $router, $template, $adapter);
    }
}
