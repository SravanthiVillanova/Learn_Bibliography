<?php

namespace App\Action\WorkType;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;

class MergeDuplicateOptionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $adapter = $container->get(Adapter::class);
        //return new MergeDuplicateOptionAction($router, $template, $adapter);
        return new \App\Action\SimpleRenderAction('app::worktype::merge_duplicate_option', $router, $template, $adapter);
    }
}
