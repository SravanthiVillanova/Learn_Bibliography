<?php
use Zend\Expressive\Application;
use Zend\Db\Adapter\Adapter;
return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
        'aliases' => array(
            'db' => 'Zend\Db\Adapter\Adapter',
        ),
    ),
    'db' => array(
        'driver'    => 'mysqli',
        //'database' => 'pr_test', 
		//'database'       => 'panta_rhei',
		'database' => 'vubib_1',
        'username'  => 'root',
        'password'  => '',
		'charset'  => 'utf8',
    	
    ),
);
