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
class WorkPublisher extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor
     */
    public function __construct($adapter)
    {
        parent::__construct('work_publisher', $adapter);
    }
    
    public function updatePublisherLocation($pub_id, $loc_ids)
    {
        $callback = function ($select) use ($pub_id, $loc_ids) {
            $select->columns(['id','work_id']);
            $select->where->in('location_id', $loc_ids);
            $select->where->equalTo('publisher_id', $pub_id);
        };
        $rows = $this->select($callback)->toArray();
        $cnt = count($rows);
        for ($i=0;$i<$cnt;$i++) {
            $this->update(['location_id' => null]);
        }
    }
    
    public function updatePublisherLocationId($pub_id, $source_ids, $dest_id)
    {
        $callback = function ($select) use ($pub_id, $source_ids) {
            $select->columns(['id','work_id','publisher_id','location_id']);
            $select->where->equalTo('publisher_id', $pub_id);
            $select->where->in('location_id', $source_ids);
        };
        $rows = $this->select($callback)->toArray();
        $cnt = count($rows);
        for ($i=0;$i<$cnt;$i++) {
            $this->update(['location_id' => $dest_id]);
        }
    }
    
    public function deleteRecordByPub($pub_id)
    {
        $this->delete(['publisher_id' => $pub_id]);
        //$this->tableGateway->delete(['id' => $id]);
    }
    
    public function findNoofWorks($id)
    {
        //echo "entered";
        //echo $id;
        $callback = function ($select) use ($id) {
            //echo 'callback';
                $select->columns(
                [
                    'cnt' => new Expression(
                    'COUNT(DISTINCT(?))', ['work_id'],
                    [Expression::TYPE_IDENTIFIER]
                    )
                ]
                );
            $select->where->equalTo('publisher_id', $id);
        };
        $rows = $this->select($callback)->toArray();
        return $rows;
        //var_dump($rows);
        //echo "done";
    }
	
	public function insertRecords($wk_id,$pub_id,$pub_locid,$pub_yr,$pub_yrEnd)
	{
		for($i=0;$i<count($pub_id);$i++)
		{
			//echo "<pre>"; echo $wk_id . ' ' . $pub_id[$i] . ' ' . $pub_locid[$i] . ' ' . $pub_yr[$i] . ' ' . $pub_yrEnd[$i]; echo "</pre>";
			if(empty($pub_locid[$i]))
			{
				//echo 'id is ' . $pub_locid[$i];
				$pub_locid[$i] = NULL;
			}
			$this->insert(
				[
				'work_id' => $wk_id,
				'publisher_id' =>$pub_id[$i],
				'location_id' => $pub_locid[$i],
				'publish_month' => 0,
				'publish_year' => $pub_yr[$i],
				'publish_month_end' => NULL,
				'publish_year_end' => $pub_yrEnd[$i],
				]
			);
		}
	}
	
	public function findRecordByWorkId($wk_id)
    {	
		$callback = function ($select) use ($wk_id) {		
			$select->columns(['*']);
			$select->join('publisher', 'work_publisher.publisher_id = publisher.id', array('name'), 'left');
			$select->join('publisher_location', 'work_publisher.location_id = publisher_location.id', array('location'), 'left');
			$select->where(['work_id' => $wk_id]);		
		 };
		$rows = $this->select($callback)->toArray();
        return $rows;
    }
	
	public function deleteRecordByWorkId($id)
    {
        $this->delete(['work_id' => $id]);
    }
}
