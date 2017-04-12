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
class Work_WorkAttribute extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor
     */
    public function __construct($adapter)
    {
        parent::__construct('work_workattribute', $adapter);
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
    
    public function deleteWorkAttributeFromWork($wkat_id)
    {
        $callback = function ($select) use ($wkat_id) {
            $select->where->equalTo('workattribute_id', $wkat_id);
        };
        $rows = $this->select($callback)->toArray();
        $cnt = count($rows);
        for ($i=0;$i<$cnt;$i++) {
            $this->delete($callback);
        }
    }
    
    public function countRecordsByAttributeOption($wkat_id, $id)
    {
        $select = $this->sql->select()->where(['workattribute_id' => $wkat_id, 'value' => $id]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        return new Paginator($paginatorAdapter);
    }
    
    public function deleteRecordByValue($wkat_id, $val)
    {
        $this->delete(['workattribute_id' => $wkat_id,'value' => $val]);
    }
    
    public function updateWork_WorkAttributeValue($wkat_id, $option_first_id, $val)
    {
        $callback = function ($select) use ($wkat_id, $val) {
            $select->where->equalTo('workattribute_id', $wkat_id);
            $select->where->equalTo('value', $val);
        };
        $rows = $this->select($callback)->toArray();
        for ($i=0;$i<count($rows);$i++) {
            $this->update(
                [
                    'value' => $option_first_id,
                ],
                ['value' => $rows[$i]['value']]
            );
        }
    }
	
	public function insertRecords($wk_id,$wkat_opt)
	{
		for($i=0;$i<count($wkat_opt);$i++)
		{
			print_r($wkat_opt[$i]);
			/*$this->insert(
				[
				'work_id' => $wk_id,
				'workattribute_id' => $wkat_opt[$i]['workattribute_id'],
				'value' => $wkat_opt[$i]['id'],
				]
			);*/
		}
	}
}
