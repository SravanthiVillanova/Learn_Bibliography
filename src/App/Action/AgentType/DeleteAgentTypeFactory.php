<?php

namespace App\Action\AgentType;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;

class DeleteAgentTypeFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $adapter = $container->get(Adapter::class);
        //return new DeleteAgentTypeAction($router, $template, $adapter);
        return new \App\Action\SimpleRenderAction('app::agenttype::delete_agenttype', $router, $template, $adapter);
    }
}
