<?php

namespace App\Factory;

class ZendSessionFactory
{
    public function __invoke()
    {
        return new \Zend\Session\Container('Bibliography');
    }
}
