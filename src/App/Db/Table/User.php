<?php
/**
 * Table Definition for user.
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2017.
 * Copyright (C) University of Freiburg 2014.
 * Copyright (C) The National Library of Finland 2015.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
namespace App\Db\Table;

use Zend\Db\Sql\Select;
use Interop\Container\ContainerInterface;

/**
 * Table Definition for user.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class User extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * @var \Zend\Session\Container
     */
    private $session;

    public function _invoke(ContainerInterface $container)
    {
        $this->session = $container->get(\Zend\Session\Container::class);
    }

    /**
     * Constructor.
     */
    public function __construct($adapter)
    {
        parent::__construct('user', $adapter);        
    }
     
     public function insertRecords($newuser_name, $new_username, $new_user_pwd, $access_level)
     {
         $this->insert(
            [
            'name' => $newuser_name,
            'username' => $new_username,
            'password' => $new_user_pwd,
            'level' => $access_level,
            ]
        );
     }

    public function findRecordById($id)
    {
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        return $row;
    }

    public function deleteRecord($id)
    {
        $this->delete(['id' => $id]);
    }

    public function updateRecord($id, $name, $username, $pwd, $level)
    {
        if ($level == 'NULL') {
            $level = null;
        }
        //echo "pwd is " . $pwd;
        if (is_null($pwd)) {
            //echo "if pwd is empty ".$pwd;
            $this->update(
            [
                'name' => $name,
                'username' => $username,
                'level' => $level,
            ],
            ['id' => $id]
            );
        } else {
            //echo "else if pwd not empty ".$pwd;
            $this->update(
            [
                'name' => $name,
                'username' => $username,
                'password' => $pwd,
                'level' => $level,
            ],
            ['id' => $id]
            );
        }
    }

    public function checkUserAuthentication($username, $pwd)
    {

        /*$rowset = $this->select(array('username' => $username, 'password' => md5($pwd));
        $row = $rowset->current();
        return($row);*/
        $callback = function ($select) use ($username, $pwd) {
            $select->columns(['*']);
            $select->where->equalTo('username', $username);
            $select->where->equalTo('password', md5($pwd));
        };

        $row = $this->select($callback)->toArray();
        /*if(count($row) == 1) {
            $row['status'] = 'authenticated';
        }
        else {
            $row['status'] = 'not authenticated';
        }*/
        return $row;
    }
    
    public function changePassword($id, $pwd)
    {
        $this->update(
            [
                'password' => md5($pwd),
            ],
            ['id' => $id]
            );
    }
    
    public function isAdmin()
    {
        echo 'user is';
        var_dump($this->session->id);
    }
}
