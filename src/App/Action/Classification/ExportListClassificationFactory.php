<?php

namespace App\Action\Classification;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;

class ExportListClassificationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $adapter = $container->get(Adapter::class);
        //return new ExportListClassificationAction($router, $template, $adapter);
        return new \App\Action\SimpleRenderAction('app::classification::exportlist_classification', $router, $template, $adapter);
    }
}
