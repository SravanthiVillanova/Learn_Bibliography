<?php
/**
 * Table Definition for agent.
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
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

/**
 * Table Definition for agent.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class Agent extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor.
     */
    public function __construct($adapter)
    {
        parent::__construct('agent', $adapter);
    }

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

        return $row;
    }

    public function findInitialLetter()
    {
        $callback = function ($select) {
            $select->columns(
                    [
                        'letter' => new Expression(
                            'DISTINCT(substring(?, 1, 1))',
                            ['fname'],
                            [
                                Expression::TYPE_IDENTIFIER,

                            ]
                        ),
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
        $select = $this->sql->select();
        if ($type == 'fname') {
            $select->where->like('fname', $name.'%');
        } elseif ($type == 'lname') {
            $select->where->like('lname', $name.'%');
        } elseif ($type == 'altname') {
            $select->where->like('alternate_name', $name.'%');
        } elseif ($type == 'orgname') {
            $select->where->like('organization_name', $name.'%');
        }
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function getLikeRecords($fname)
    {
        $callback = function ($select) use ($fname) {
            $select->where->like('fname', '%'.$fname.'%');
        };
        $rows = $this->select($callback)->toArray();

        return $rows;
    }

    public function getLastNameLikeRecords($name)
    {
        $callback = function ($select) use ($name) {
            $select->where->like('lname', $name.'%');
        };
        $rows = $this->select($callback)->toArray();

        return $rows;
    }
}
