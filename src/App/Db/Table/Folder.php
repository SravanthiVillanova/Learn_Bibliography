<?php
/**
 * Table Definition for record
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
 * @package  Db_Table
 * @author   Markus Beh <markus.beh@ub.uni-freiburg.de>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
namespace App\Db\Table;

use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

/**
 * Table Definition for record
 *
 * @category VuFind
 * @package  Db_Table
 * @author   Markus Beh <markus.beh@ub.uni-freiburg.de>
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
class Folder extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor
     */
    public function __construct($adapter)
    {
        parent::__construct('folder', $adapter);
    }
    
    /**
     * Update an existing entry in the record table or create a new one
     *
     * @param string $id      Record ID
     * @param string $source  Data source
     * @param string $rawData Raw data from source
     *
     * @return Updated or newly added record
     */
    
    public function findParent()
    {
        $select = $this->sql->select()->where(['parent_id' => null]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        return new Paginator($paginatorAdapter);
    }
	
	public function exportClassification($parent)
	{
		//$fl = new Folder($this->adapter);
        //$subselect = $wtwa->getWorkAttributeQuery($id);
		$callback = function ($select) {
            $select->columns(['*']);
			$select->where('parent_id IS NULL');
			};
			$row = $this->select($callback)->toArray();
			foreach($row as $t):
			//echo "<pre>"; print_r($t); echo "</pre>";
			$id = $t['id'];
				//$rc = $fl->getDepth($t['id']);
				$callback = function ($select) use ($id){
					$select->columns(['*']);
					$select->where->equalTo('parent_id', $id);
				};
				$rc = $this->select($callback)->toArray();
				//var_dump($rc);												
				/*while(count($rc) > 0) {
					
				}*/
			endforeach;
			/*echo "rows are ";
			echo "<pre>"; print_r($row); echo "</pre>";*/
	}
	
	public function getDepth($id)
	{
		$depth = 0;
		$current_parent_id = $id;
		while(!is_null($current_parent_id)) {
			$callback = function ($select) use ($current_parent_id){
				$select->columns(['*']);
				$select->where->equalTo('parent_id', $current_parent_id);
			};
			$rc = $this->select($callback)->toArray();
			$current_parent_id = $rc['id'];
			$depth += 1;
		}
	}
}