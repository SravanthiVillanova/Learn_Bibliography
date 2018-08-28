<?php

return [
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
        'aliases' => [
            'db' => 'Zend\Db\Adapter\Adapter',
        ],
    ],
    'db' => [
        'driver'    => 'mysqli',
        //'database' => 'pr_test',
        //'database'       => 'panta_rhei',
        'database' => 'vubib_1',
        'username'  => 'root',
        'password'  => '',
        'charset'  => 'utf8',

    ],
];
