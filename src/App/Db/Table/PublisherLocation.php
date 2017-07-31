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
class PublisherLocation extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor.
     */
    public function __construct($adapter)
    {
        parent::__construct('publisher_location', $adapter);
    }

    /**
     * Update an existing entry in the record table or create a new one.
     *
     * @param string $id      Record ID
     * @param string $source  Data source
     * @param string $rawData Raw data from source
     *
     * @return Updated or newly added record
     */
    public function insertRecords($name)
    {
        $this->insert(
            [
            'name' => $name,
            ]
        );
    }

    public function findRecords($location)
    {
        $select = $this->sql->select();
       // $select->columns(array('location'));
       $select->join('publisher', 'publisher_location.publisher_id = publisher.id', array('name'), 'inner');
        $select->where(['location' => $location]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function deletePublisherRecord($id, $locs)
    {
        // print_r($locs);
       if (($id != null) && (count($locs) == 0)) {
           $this->delete(['publisher_id' => $id]);
        //$this->tableGateway->delete(['id' => $id]);
       }
        if (($id != null) && (count($locs) >= 1)) {
            $callback = function ($select) use ($id, $locs) {
                $select->where->in('location', $locs);
                $select->where->equalTo('publisher_id', $id);
            };
            $this->delete($callback);
        }
    }

    public function deletePublisherRecordById($id, $loc_ids)
    {
        $callback = function ($select) use ($id, $loc_ids) {
            $select->where->in('id', $loc_ids);
            $select->where->equalTo('publisher_id', $id);
        };
        //$this->delete($callback);
        $rows = $this->select($callback)->toArray();
        $cnt = count($rows);
        for ($i = 0; $i < $cnt; ++$i) {
            $this->delete($callback);
        }
    }

    public function addPublisherLocation($id, $loc)
    {
        $this->insert(
            [
            'publisher_id' => $id,
            'location' => $loc,
            ]
        );
    }

    public function findPublisherLocations($id)
    {
        $select = $this->sql->select()->where(['publisher_id' => $id]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function findPublisherId($id)
    {
        $rowset = $this->select(array('publisher_id' => $id));
        $row = $rowset->current();

        return $row;
    }

    public function findLocationId($id, $locs)
    {
        /*echo "entered";
        echo $id ."\n";
        print_r($locs); */
        $callback = function ($select) use ($id, $locs) {
            $select->where->in('location', $locs);
            $select->where->equalTo('publisher_id', $id);
        };

        return $this->select($callback)->toArray();
    }

    public function getPublisherLocations($pub_id)
    {
        $callback = function ($select) use ($pub_id) {
            $select->where->equalTo('publisher_id', $pub_id);
        };
        $rows = $this->select($callback)->toArray();
        //var_dump($rows);
        return $rows;
    }
    
    public function movePublisher($pub_src_id, $pub_dest_id, $src_loc_id)
    {
        //update publoc set pubid = destpubid where pubid=srcpubid and id=$source_locid
        $this->update(
            [
                'publisher_id' => $pub_dest_id,
            ],
            ['publisher_id' => $pub_src_id, 'id' => $src_loc_id]
        );
    }
    
    public function mergePublisher($src_loc_id)
    {
        $this->delete(['id' => $src_loc_id]);
    }
}
