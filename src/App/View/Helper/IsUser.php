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
        if ($this->session->id != null) {
            return true;
        }

        return false;
    }

    public function getUser()
    {
        if ($this->session->id != null) {
            return $this->session->id;
        }

        return null;
    }

    public function isInsSet($str)
    {
        //fetch instructions
        $table = new \App\Db\Table\Page_Instructions($this->adapter);
        $ins = $table->findRecordByPageName($str);

        return $ins;
    }

    public function getUserType()
    {
        $usr_typ = '';

        $table = new \App\Db\Table\User($this->adapter);
        $usr = $table->findRecordById($this->session->id);

        if (isset($usr['level'])) {
            if ($usr['level'] == 1) {
                $usr['level'] = 'Administrator';
            } else {
                $usr['level'] = 'Super User';
            }
        } else {
            $usr['level'] = 'User';
        }

        return $usr;
    }

    public function getRvwCount()
    {
        $table = new \App\Db\Table\Work($this->adapter);
        $cnt = $table->getPendingReviewWorksCount();

        return $cnt;
    }
}
