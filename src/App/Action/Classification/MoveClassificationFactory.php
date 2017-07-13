<?php

namespace App\Action\Classification;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;

class MoveClassificationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $adapter = $container->get(Adapter::class);
        //return new MoveClassificationAction($router, $template, $adapter);
        return new \App\Action\SimpleRenderAction('app::classification::move_classification', $router, $template, $adapter);
    }
}
