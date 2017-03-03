<?php
namespace App\View\Helper;

use Zend\View\Helper\ViewModel;
use Interop\Container\ContainerInterface;

class HasPermissionFactory
{
    public function __invoke($container)
    {
		$adapter = $container->get(\Zend\Db\Adapter\Adapter::class);
		$session = $container->get(\Zend\Session\Container::class);
		return new HasPermission($adapter,$session);
    }
}
