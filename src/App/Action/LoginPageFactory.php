<?php

namespace App\Action;

//use App\Entity\LoginUser;
use App\Repository\UserAuthenticationInterface;
use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresConfigId;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;

class LoginPageFactory implements RequiresConfigId
{
    use ConfigurationTrait;

    public function dimensions()
    {
        return ['app'];
    }

    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $userRepository = $container->get(UserAuthenticationInterface::class);
        $adapter = $container->get(Adapter::class);
        //$userEntity = new LoginUser();

        $authenticationOptions = $this->options($container->get('config'), 'authentication');

        return new LoginPageAction(
            $router,
            $template,
            $userRepository,
            //$userEntity,
            $authenticationOptions['default_redirect_to'], $adapter,
            $container->get(\Zend\Session\Container::class)
        );
    }
}
