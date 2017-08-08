<?php
/**
 * Table Definition for workattribute_option.
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

/**
 * Table Definition for workattribute_option.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class WorkAttribute_Option extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor.
     */
    public function __construct($adapter)
    {
        parent::__construct('workattribute_option', $adapter);
    }

    public function deleteWorkAttributeOptions($wkat_id)
    {
        $callback = function ($select) use ($wkat_id) {
            $select->where->equalTo('workattribute_id', $wkat_id);
        };
        $rows = $this->select($callback)->toArray();
        $cnt = count($rows);
        for ($i = 0; $i < $cnt; ++$i) {
            $this->delete($callback);
        }
    }

    public function displayAttributeOptions($wkat_id)
    {
        $select = $this->sql->select();
        $select->where->equalTo('workattribute_id', $wkat_id);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function addOption($wkat_id, $title, $val)
    {
        $this->insert(
            [
            'workattribute_id' => $wkat_id,
            'title' => $title,
            'value' => $val,
            ]
        );
    }

    public function findRecordById($id)
    {
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        return $row;
    }

    public function updateOption($id, $title, $val)
    {
        $this->update(
            [
                'title' => $title,
                'value' => $val,
            ],
            ['id' => $id]
        );
    }

    public function deleteOption($wkat_id, $id)
    {
        $this->delete(
            [
                'workattribute_id' => $wkat_id,
                'id' => $id,
            ]
        );
    }

    public function getDuplicateOptions($wkat_id)
    {
        $select = $this->sql->select();
        $select->where->equalTo('workattribute_id', $wkat_id);
        $select->group('title');
        $select->having('count(title) > 1');

        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function getDuplicateOptionRecords($wkat_id, $option_dup_title, $option_dup_id)
    {
        $callback = function ($select) use ($wkat_id, $option_dup_title, $option_dup_id) {
            $select->where->equalTo('title', $option_dup_title);
            $select->where->equalTo('workattribute_id', $wkat_id);
            $select->where->notEqualTo('id', $option_dup_id);
        };
        $rows = $this->select($callback)->toArray();
        //echo "no od dup rows is" . count($rows);
        //echo "<pre>"; print_r($rows); echo "</pre>";
        return $rows;
    }

    public function getAttributeOptions($opt_title, $wkat_id)
    {
        $callback = function ($select) use ($opt_title, $wkat_id) {
            $select->where->like('title', $opt_title.'%');
            $select->where->equalTo('workattribute_id', $wkat_id);
        };
        $rows = $this->select($callback)->toArray();

        return $rows;
    }

    public function getOptionIds($wkat_id, $opt_title)
    {
        $rows = [];
        for ($i = 0; $i < count($wkat_id); ++$i) {
            $wkatid = $wkat_id[$i];
            $opttitle = $opt_title[$i];
            $callback = function ($select) use ($wkatid, $opttitle) {
                $select->where->equalTo('workattribute_id', $wkatid);
                $select->where->equalTo('title', $opttitle);
            };

            $rows = $rows + $this->select($callback)->toArray() + $rows;
            echo 'count is '.$i;
            echo '<pre>';
            print_r($rows);
            echo '</pre>';
        }
        //echo "<pre>";print_r($rows);echo "</pre>";
        //$rows = $this->select($callback)->toArray();
        //return $rows;
    }

    public function getOptionTitle($id, $wkat_id)
    {
        //echo "id is $id <br />";
        //echo "wkat_id is $wkat_id <br />";
        $rowset = $this->select(array('id' => $id, 'workattribute_id' => $wkat_id));
        $row = $rowset->current();
        //var_dump($row);
        return $row;
    }
}
