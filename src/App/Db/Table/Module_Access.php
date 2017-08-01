<?php
/**
 * Table Definition for record.
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
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
 * @category VuFind
 *
 * @author   Markus Beh <markus.beh@ub.uni-freiburg.de>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://vufind.org Main Site
 */

namespace App\Db\Table;

use Zend\Db\Sql\Select;
use Interop\Container\ContainerInterface;

/**
 * Table Definition for record.
 *
 * @category VuFind
 *
 * @author   Markus Beh <markus.beh@ub.uni-freiburg.de>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://vufind.org Main Site
 */
class Module_Access extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * @var \Zend\Session\Container
     */
    private $session;

    /*public function _invoke(ContainerInterface $container) {

        $this->session = $container->get(\Zend\Session\Container::class);
    }*/

    /**
     * Constructor.
     */
    public function __construct($adapter)
    {
        parent::__construct('module_access', $adapter);

        //var_dump($ses);
        //ContainerInterface $container;
        $this->session = new \Zend\Session\Container('Bibliography');
    }
     /**
      * Update an existing entry in the record table or create a new one.
      *
      * @param string $id      Record ID
      * @param string $source  Data source
      * @param string $rawData Raw data from source
      *
      * @return Updated or newly added record
      */
    public function getModules($role)
    {
         $v_role = $role;
        //if($role == 'role_a') {
            $callback = function ($select) use ($v_role) {
                $select->columns(['module']);
                $select->where->equalTo($v_role, 1);
            };
         $row = $this->select($callback)->toArray();
            return $row;
        //}
    }
    
	public function setModuleAccess($module,$role)
	{
		/*echo "module is $module <br />";
		echo "role is $role <br />";
		echo "val is $val <br />";*/
		if($role == 'Super User') 
		{
			$role = 'role_su';
		}
		elseif ($role == 'User')
		{
			$role = 'role_u';
		}
		$this->update(
            [
                $role => 1,
            ],
            ['module' => $module]
            );
	}
	
	public function unsetModuleAccess($module,$role)
	{
		if($role == 'Super User') 
		{
			$role = 'role_su';
		}
		elseif ($role == 'User')
		{
			$role = 'role_u';
		}
		$this->update(
            [
                $role => 0,
            ],
            ['module' => $module]
        );
	}
	
	public function getAllModules()
	{
		$callback = function ($select) {
            $select->columns(['module']);
        };
        $rows = $this->select($callback)->toArray();
		$mod_rows = array_column($rows, 'module');
        return $mod_rows;
	}
}
