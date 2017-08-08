<?php
/**
 * Table Definition for publisher.
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
 * Table Definition for publisher.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class Publisher extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor.
     */
    public function __construct($adapter)
    {
        parent::__construct('publisher', $adapter);
    }

    public function insertRecords($name)
    {
        $this->insert(
            [
            'name' => $name,
            ]
        );
    }

    public function findRecords($name)
    {
        $select = $this->sql->select();
        $select->where->like('name', $name.'%');
        //->where(['name' => $name]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function findRecordById($id)
    {
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        return $row;
    }

    public function updateRecord($id, $name)
    {
        $this->update(
            [
                'name' => $name,
            ],
            ['id' => $id]
        );
    }

    public function deleteRecord($id)
    {
        $this->delete(['id' => $id]);
        //$this->tableGateway->delete(['id' => $id]);
    }

    public function findInitialLetter()
    {
        $callback = function ($select) {
            $select->columns(
                    [
                        'letter' => new Expression(
                            'DISTINCT(substring(?, 1, 1))',
                            ['name'],
                            [Expression::TYPE_IDENTIFIER]
                        ),
                    ]
                );
            $select->order('name');
        };

        return $this->select($callback)->toArray();
    }

    public function displayRecordsByName($letter)
    {
        $select = $this->sql->select();
        $select->where->like('name', $letter.'%');
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function getLikeRecords($name)
    {
        $callback = function ($select) use ($name) {
            $select->where->like('name', '%'.$name.'%');
        };
        $rows = $this->select($callback)->toArray();

        return $rows;
    }
}
