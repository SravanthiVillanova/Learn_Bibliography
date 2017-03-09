<?php
namespace App\View\Helper;
use Interop\Container\ContainerInterface;

class IsUser extends \Zend\View\Helper\AbstractHelper
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
		//var_dump($this->session->modules_access);
		//return 'hello, ' . $this->session->id . 'has access to';
		 //. implode($this->session->modules_access)
		return $this;
		 //return $this->session->modules_access;
    }
	
	public function hasPermission()
	{
		return $this->session->modules_access;
	}
	
	public function isLogged()
	{
		if($this->session->id != NULL)
			return true;
		return false;
	}
}
