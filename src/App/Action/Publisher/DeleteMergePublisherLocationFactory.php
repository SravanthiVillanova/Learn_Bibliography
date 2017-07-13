<?php

namespace App\Action\Publisher;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;

class DeleteMergePublisherLocationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $adapter = $container->get(Adapter::class);
        //return new DeleteMergePublisherLocationAction($router, $template, $adapter);
        return new \App\Action\SimpleRenderAction('app::publisher::delete_merge_publisher_location', $router, $template, $adapter);
    }
}
