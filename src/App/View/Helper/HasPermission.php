<?php
namespace App\View\Helper;
use Interop\Container\ContainerInterface;

class HasPermission extends \Zend\View\Helper\AbstractHelper
{
	private $session;
	
	private $adapter;
	
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, $session)
	{
		$this->adapter = $adapter;
		$this->session = $session;
	}

    public function __invoke()
    {
		//test_cnt(ContainerInterface $container);
		//$table = new \App\Db\Table\User($this->adapter);
		//$user = $table->isAdmin();
		return 'hello, ' . $this->session->id . 'has access to' . implode($this->session->modules_access);
    }
	
	//public function test_cnt(ContainerInterface $container)
	//{
		//$container = new \Interop\Container\ContainerInterface::interface;
		//$table = new \App\Db\Table\User((new \Interop\Container\ContainerInterface $container)->get(Adapter::class));
		//$user = $table->isAdmin();
	//}
}
