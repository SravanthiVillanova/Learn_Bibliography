<?php

namespace App\Action\AgentType;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;

class NewAgentTypeFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $adapter = $container->get(Adapter::class);
        //return new NewAgentTypeAction($router, $template, $adapter);
        return new \App\Action\SimpleRenderAction('app::agenttype::new_agenttype', $router, $template, $adapter);
    }
}
