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
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;

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
class WorkAgent extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor.
     */
    public function __construct($adapter)
    {
        parent::__construct('work_agent', $adapter);
    }

    public function deleteRecordByAgentTypeId($id)
    {
        $this->delete(['agenttype_id' => $id]);
        //$this->tableGateway->delete(['id' => $id]);
    }

    public function deleteRecordByAgentId($id)
    {
        $this->delete(['agent_id' => $id]);
        //$this->tableGateway->delete(['id' => $id]);
    }

    public function countRecordsByAgentType($id)
    {
        $select = $this->sql->select()->where(['agenttype_id' => $id]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function countRecordsByAgent($id)
    {
        $select = $this->sql->select()->where(['agent_id' => $id]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function insertRecords($wk_id, $ag_id, $agt_id)
    {
        for ($i = 0; $i < count($ag_id); ++$i) {
            if ($ag_id[$i] != null && $agt_id[$i] != null) {
                $this->insert(
                [
                'work_id' => $wk_id,
                'agent_id' => $ag_id[$i],
                'agenttype_id' => $agt_id[$i],
                ]
            );
            }
        }
    }

    public function findRecordByWorkId($wk_id)
    {
        $rows = [];
        $callback = function ($select) use ($wk_id) {
            $select->columns(['*']);
            $select->join('agenttype', 'work_agent.agenttype_id = agenttype.id', array('type'), 'left');
            $select->join('agent', 'work_agent.agent_id = agent.id', array('fname', 'lname', 'alternate_name', 'organization_name'), 'left');
            $select->where(['work_id' => $wk_id]);
        };

        $rows = $this->select($callback)->toArray();

        return $rows;
    }

    public function deleteRecordByWorkId($id)
    {
        $this->delete(['work_id' => $id]);
    }

    public function findRecordByAgentId($ag_id)
    {
        $callback = function ($select) use ($ag_id) {
            $select->columns(['*']);
            $select->where->equalTo('agent_id', $ag_id);
        };
        $rows = $this->select($callback)->toArray();

        return $rows;
    }

    public function updateRecordByAgentId($src_ag_id, $dst_ag_id)
    {
        $this->update(
            [
                'agent_id' => $src_ag_id,
            ],
            ['agent_id' => $dst_ag_id]
        );
    }

    public function updateRecords($wk_id, $agent_id, $agent_type)
    {
        for ($i = 0; $i < count($agent_type); ++$i) {
            if (empty($agent_id[$i])) {
                $agent_id[$i] = 0;
            }
            $this->update(
                [
                'agent_id' => $agent_id[$i],
                'agenttype_id' => $agent_type[$i],
                ],
                ['work_id' => $wk_id]
            );
        }
    }
}
