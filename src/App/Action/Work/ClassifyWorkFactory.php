<?php

namespace App\Action\Work;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Expressive\Helper\ServerUrlHelper;

class ClassifyWorkFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $adapter = $container->get(Adapter::class);
        //$helper = $container->get(ServerUrlHelper::class);
        //return new ClassifyWorkAction($router, $template, $adapter, $helper);
        return new \App\Action\SimpleRenderAction('app::work::classify_work', $router, $template, $adapter);
    }
}
