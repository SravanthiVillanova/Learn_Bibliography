<?php

namespace App\Factory;

class ZendSessionFactory
{
    public function __invoke()
    {
        $session = new \Zend\Session\Container('Bibliography');
		$session->setExpirationSeconds( 3600 );
		//return new \Zend\Session\Container('Bibliography');
		return $session;
    }
}
