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
class Agent extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor
     */
    public function __construct($adapter)
    {
        parent::__construct('agent', $adapter);
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
    public function insertRecords($fname, $lname, $altname, $orgname)
    {
        $this->insert(
            [
            'fname' => $fname,
            'lname' => $lname,
            'alternate_name' => $altname,
            'organization_name' => $orgname,
            ]
        );
    }
    
    public function updateRecord($id, $fname, $lname, $altname, $orgname)
    {
        $this->update(
            [
                'fname' => $fname,
                'lname' => $lname,
                'alternate_name' => $altname,
                'organization_name' => $orgname,
            ],
            ['id' => $id]
        );
    }
    
    public function deleteRecord($id)
    {
        //echo "id to del is " . $id . "<br />";
		$this->delete(['id' => $id]);
    }
    
    public function findRecordById($id)
    {
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        return($row);
    }
    
    public function findInitialLetter()
    {
        /* $callback = function ($select) {
                $select->columns(
                    [
                        'letter' => new Expression(
                            'DISTINCT(substring(concat(?,?), 1, 1))',
                            ['lname','fname'],
                            [
                                Expression::TYPE_IDENTIFIER,
                                Expression::TYPE_IDENTIFIER
                            ]
                        )
                    ]
                );
            $select->order(
                    [
                        'name' => new Expression(
                            'CONCAT(?,?)',
                            ['lname','fname'],
                            [
                                Expression::TYPE_IDENTIFIER,
                                Expression::TYPE_IDENTIFIER
                            ]
                        )
                    ]
            );
            //('fname ASC');
        };*/
        
        $callback = function ($select) {
            $select->columns(
                    [
                        'letter' => new Expression(
                            'DISTINCT(substring(?, 1, 1))',
                            ['fname'],
                            [
                                Expression::TYPE_IDENTIFIER
                               
                            ]
                        )
                    ]
                );
            $select->order('fname');
            //('fname ASC');
        };
        
        return $this->select($callback)->toArray();
    }
    
    public function displayRecordsByName($letter)
    {
        $select = $this->sql->select();
        $select->where->like('fname', $letter.'%');
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        return new Paginator($paginatorAdapter);
    }
    
    public function findRecords($name, $type)
    {
        if ($type == 'fname') {
            $select = $this->sql->select()->where(['fname' => $name]);
        } elseif ($type == 'lname') {
            $select = $this->sql->select()->where(['lname' => $name]);
        } elseif ($type == 'altname') {
            $select = $this->sql->select()->where(['alternate_name' => $name]);
        } elseif ($type == 'orgname') {
            $select = $this->sql->select()->where(['organization_name' => $name]);
        }
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        return new Paginator($paginatorAdapter);
    }
	
	public function getLikeRecords($fname)
	{
		 $callback = function ($select) use ($fname) {
            $select->where->like('fname', '%'. $fname . '%');           
        };
        $rows = $this->select($callback)->toArray();
        return $rows;
	}
	
	public function getLastNameLikeRecords($name)
	{
		 $callback = function ($select) use ($name) {
            $select->where->like('lname', $name . '%');           
        };
        $rows = $this->select($callback)->toArray();
        return $rows;
	}
}
