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
class Work_Folder extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor
     */
    public function __construct($adapter)
    {
        parent::__construct('work_folder', $adapter);
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
    
    public function getWorkFolder()
    {
        //echo $id;
        /*$subselect = $this->sql->select()->distinct('work_id');
        //$subselect->join('work', 'work_folder.work_id = work.id', 'inner');
		//$subselect->columns([new Expression(DISTINCT('work_id'))]);
        
        $paginatorAdapter = new Paginator(new DbSelect($subselect, $this->adapter));
        $cnt = $paginatorAdapter->getTotalItemCount();
        //echo "selected field count is $cnt <br />";
        $paginatorAdapter->setDefaultItemCountPerPage($cnt);
        $workIds = [];
        foreach ($paginatorAdapter as $row) :
                $workIds[] = $row['id'];
        endforeach;
        //print_r($fieldRows);
        return $workIds;*/
		
		 $callback = function ($select) {
            $select->columns(
                    [
                        'work_id' => new Expression(
                            'DISTINCT(?)',
                            ['work_id'],
                            [Expression::TYPE_IDENTIFIER]
                        )
                    ]
                );
        };
        
        $rows = $this->select($callback)->toArray();
		$workIds = [];
        foreach ($rows as $row) :
                $workIds[] = $row['work_id'];
        endforeach;
        return $workIds;
    } 
	
	public function insertRecords($wk_id,$folder_id)
	{
		$this->insert(
			[
			'work_id' => $wk_id,
			'folder_id' => $folder_id,
			]
		);
	}
	
	public function findRecordByWorkId($id)
    {
        $rowset = $this->select(array('work_id' => $id));
        $row = $rowset->current();
        return($row);
    }
}
