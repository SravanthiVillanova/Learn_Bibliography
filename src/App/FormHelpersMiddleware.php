<?php

namespace App;

use Zend\Form\View\HelperConfig as FormHelperConfig;
use Zend\View\HelperPluginManager;

class FormHelpersMiddleware
{
    private $helpers;

    public function __construct(HelperPluginManager $helpers)
    {
        $this->helpers = $helpers;
    }

    public function __invoke($request, $response, callable $next)
    {
        $config = new FormHelperConfig();
        $config->configureServiceManager($this->helpers);

        return $next($request, $response);
    }
}
