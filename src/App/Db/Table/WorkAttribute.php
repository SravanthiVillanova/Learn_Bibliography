<?php
/**
 * Table Definition for workattribute.
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
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
namespace App\Db\Table;

use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Sql;

/**
 * Table Definition for workattribute.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class WorkAttribute extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * WorkAttribute constructor.
     *
     * @param Adapter $adapter for db connection
     */
    public function __construct($adapter)
    {
        parent::__construct('workattribute', $adapter);
    }

    /**
     * Fetch workattributes
     *
     * @return Paginator $paginatorAdapter workattributes
     */
    public function displayAttributes()
    {
        $select = $this->sql->select();
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    /**
     * Fetch worktype and their workattributes
     *
     * @param Integer $id work type id
     *
     * @return Array $rows worktype and their attributes
     */
    public function displayAttributes1($id)
    {
        $wtwa = new WorkType_WorkAttribute($this->adapter);
        $subselect = $wtwa->getWorkAttributeQuery($id);

        $callback = function ($select) use ($subselect) {
            $select->columns(['id', 'field']);
            $select->where->notIn('field', $subselect);
        };
        $rows = $this->select($callback)->toArray();

        return $rows;
    }

    /**
     * Add attribute
     *
     * @param string $field work attribute name
     * @param string $type  work attribute type
     *
     * @return empty
     */
    public function addAttribute($field, $type)
    {
        $this->insert(
            [
            'field' => $field,
            'type' => $type,
            ]
        );
    }

    /**
     * Find record using workattribute id
     *
     * @param Integer $id workattribute id
     *
     * @return Array $row workattributes
     */
    public function findRecordById($id)
    {
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        return $row;
    }

    /**
     * Update record
     *
     * @param Integer $id    workattribute id
     * @param string  $field workattribute name
     *
     * @return empty
     */
    public function updateRecord($id, $field)
    {
        $this->update(
            [
                'field' => $field,
            ],
            ['id' => $id]
        );
    }

    /**
     * Delete record
     *
     * @param Integer $id workattribute id
     *
     * @return empty
     */
    public function deleteRecord($id)
    {
        $this->delete(['id' => $id]);
    }

    /**
     * Get attributes of a worktype
     *
     * @param Integer $id worktype id
     *
     * @return Paginator $paginatorAdapter workattributes
     */
    public function getAttributesForWorkType($id)
    {
        $subselect = $this->sql->select();
        $subselect->join('worktype_workattribute', 'workattribute.id = worktype_workattribute.workattribute_id', array(), 'inner');
        $subselect->where(['worktype_id' => $id]);
        $subselect->order('rank');

        $paginatorAdapter = new DbSelect($subselect, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    /**
     * Find record using workattribute field
     *
     * @param String $attribute workattribute field
     *
     * @return Array $row workattributes
     */
    public function getAttributeRecord($attribute)
    {
        $rowset = $this->select(array('field' => $attribute));
        $row = $rowset->current();

        return $row;
    }
}
