<?php

namespace App;

use Zend\View\HelperPluginManager;

class FormHelpersMiddlewareFactory
{
    public function __invoke($container)
    {
        return new FormHelpersMiddleware(
            $container->get(HelperPluginManager::class)
        );
    }
}
