<?php
/**
 * Table Definition for worktype_workattribute.
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
 * Table Definition for worktype_workattribute.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class WorkType_WorkAttribute extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor.
     */
    public function __construct($adapter)
    {
        parent::__construct('worktype_workattribute', $adapter);
    }

    public function displayRanks($id)
    {
        $select = $this->sql->select();
        $select->join('workattribute', 'worktype_workattribute.workattribute_id = workattribute.id', array('field'), 'inner');
        $select->where(['worktype_id' => $id]);
        $select->order('rank');

        $paginatorAdapter = new Paginator(new DbSelect($select, $this->adapter));
        //$cnt = $paginatorAdapter->getTotalItemCount();

        return $paginatorAdapter;
    }

    public function getWorkAttributeQuery($id)
    {
        //echo $id;
        $subselect = $this->sql->select();
        $subselect->join('workattribute', 'worktype_workattribute.workattribute_id = workattribute.id', array('field'), 'inner');
        $subselect->where(['worktype_id' => $id]);
        $subselect->order('rank');

        $paginatorAdapter = new Paginator(new DbSelect($subselect, $this->adapter));
        $cnt = $paginatorAdapter->getTotalItemCount();
        //echo "selected field count is $cnt <br />";
        $paginatorAdapter->setDefaultItemCountPerPage($cnt);
        $fieldRows = [];
        foreach ($paginatorAdapter as $row) :
                $fieldRows[] = $row['field'];
        endforeach;
        //print_r($fieldRows);
        return $fieldRows;
    }

    public function addAttributeToWorkType($wkt_id, $wkat_ids)
    {
        $cnt = count($wkat_ids);
        for ($i = 0; $i < $cnt; ++$i) {
            $this->insert(
            [
                'worktype_id' => $wkt_id,
                'workattribute_id' => $wkat_ids[$i],
                'rank' => new Expression('999'.$i),
            ]
            );
        }
    }

    public function deleteAttributeFromWorkType($wkt_id, $wkat_ids)
    {
        $callback = function ($select) use ($wkt_id, $wkat_ids) {
            $select->where->in('workattribute_id', $wkat_ids);
            $select->where->equalTo('worktype_id', $wkt_id);
            //$select->order('rank');
        };
        $rows = $this->select($callback)->toArray();
        $cnt = count($rows);
        for ($i = 0; $i < $cnt; ++$i) {
            $this->delete($callback);
        }
    }

    public function updateWorkTypeAttributeRank($wkt_id, $wkatids)
    {
        $wkat_ids = explode(',', $wkatids);
        foreach ($wkat_ids as $id) :
            $sort_wkatids[] = (int) preg_replace("/^\w{2,3}_/", '', $id);
        endforeach;
        $callback = function ($select) use ($wkt_id) {
            $select->where->equalTo('worktype_id', $wkt_id);
            $select->order('rank');
        };
        $rows = $this->select($callback)->toArray();
        $cnt = count($rows);
        //to avoid primary key conflicts, set records rank wise first
        for ($i = 0; $i < $cnt; ++$i) {
            $this->update(
            [
                'rank' => new Expression('1999'.$i),
            ],
            ['workattribute_id' => $sort_wkatids[$i]]
            );
        }
        //update ranks
        for ($i = 0; $i < $cnt; ++$i) {
            $this->update(
            [
                'rank' => $i,
            ],
            ['workattribute_id' => $sort_wkatids[$i]]
            );
        }
    }

    public function countWorkTypesByWorkAttributes($wkat_id)
    {
        $callback = function ($select) use ($wkat_id) {
            $select->columns(
                [
                    'count_worktypes' => new Expression(
                    'COUNT(?)', ['worktype_id'],
                    [Expression::TYPE_IDENTIFIER]
                    ),
                ]
                );
            $select->where->equalTo('workattribute_id', $wkat_id);
        };

        return $this->select($callback)->toArray();
    }

    public function deleteRecordByWorkType($wkt_id)
    {
        $callback = function ($select) use ($wkt_id) {
            $select->where->equalTo('worktype_id', $wkt_id);
        };
        $rows = $this->select($callback)->toArray();
        $cnt = count($rows);
        for ($i = 0; $i < $cnt; ++$i) {
            $this->delete($callback);
        }
    }

    public function deleteAttributeFromAllWorkTypes($wkat_id)
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

    public function findRecordById($wkt_id)
    {
        /*$rowset = $this->select(array('worktype_id' => $wkt_id));
        $row = $rowset->current();
        var_dump($row);
        //return($row);*/
        $callback = function ($select) use ($wkt_id) {
            $select->columns(['*']);
            $select->where->equalTo('worktype_id', $wkt_id);
        };
        $rows = $this->select($callback)->toArray();
        //var_dump(count($rows));
        return $rows;
        //var_dump($rows);
    }
}
