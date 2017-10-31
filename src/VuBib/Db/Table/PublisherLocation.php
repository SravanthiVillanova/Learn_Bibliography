<?php
/**
 * Table Definition for publisher_location.
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
namespace VuBib\Db\Table;

use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;

/**
 * Table Definition for publisher_location.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class PublisherLocation extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * PublisherLocation constructor.
     *
     * @param Adapter $adapter for db connection
     */
    public function __construct($adapter)
    {
        parent::__construct('publisher_location', $adapter);
    }

    /**
     * Find publisher record based on location
     *
     * @param String $location publisher location name
     *
     * @return Paginator $paginatorAdapter publisher record along with location
     */
    public function findRecords($location)
    {
        $select = $this->sql->select();
        // $select->columns(array('location'));
        $select->join(
            'publisher', 'publisher_location.publisher_id = publisher.id',
            array('name'), 'inner'
        );
        //$select->where->like('location', $location.'%');
        $select->where(['location' => $location]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    /**
     * Delete publisher location record.
     *
     * @param Number $id   publisher id
     * @param Array  $locs publisher locations
     *
     * @return empty
     */
    public function deletePublisherRecord($id, $locs)
    {
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

    /**
     * Delete publisher location record.
     *
     * @param Number $id      publisher id
     * @param Array  $loc_ids ids of publisher locations
     *
     * @return empty
     */
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

    /**
     * Insert publisher location record.
     *
     * @param Number $id  publisher id
     * @param String $loc publisher location name
     *
     * @return empty
     */
    public function addPublisherLocation($id, $loc)
    {
        $this->insert(
            [
            'publisher_id' => $id,
            'location' => $loc,
            ]
        );
    }

    /**
     * Find publisher location
     *
     * @param Number $id publisher id
     *
     * @return Paginator $paginatorAdapter publisher record along with location
     */
    public function findPublisherLocations($id)
    {
        $select = $this->sql->select()->where(['publisher_id' => $id]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    /**
     * Find first publisher location by publisher id
     *
     * @param Number $id publisher id
     *
     * @return Array $row publisher location record
     */
    public function findPublisherId($id)
    {
        $rowset = $this->select(array('publisher_id' => $id));
        $row = $rowset->current();

        return $row;
    }

    /**
     * Find publisher locations using publisher id,location names
     *
     * @param Number $id   publisher id
     * @param Array  $locs publisher locations
     *
     * @return Array
     */
    public function findLocationId($id, $locs)
    {
        $callback = function ($select) use ($id, $locs) {
            $select->where->in('location', $locs);
            $select->where->equalTo('publisher_id', $id);
        };

        return $this->select($callback)->toArray();
    }

    /**
     * Get all the publisher locations using publisher id
     *
     * @param Number $pub_id publisher id
     *
     * @return Array
     */
    public function getPublisherLocations($pub_id)
    {
        $callback = function ($select) use ($pub_id) {
            $select->where->equalTo('publisher_id', $pub_id);
        };
        $rows = $this->select($callback)->toArray();
        return $rows;
    }
    
    /**
     * Move publisher location
     *
     * @param Number $pub_src_id  source publisher id
     * @param Number $pub_dest_id destination publisher id
     * @param Number $src_loc_id  id of source publisher location
     *
     * @return empty
     */
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

    /**
     * Merge publisher location
     *
     * @param Number $src_loc_id id of source publisher location
     *
     * @return empty
     */
    public function mergePublisher($src_loc_id)
    {
        $this->delete(['id' => $src_loc_id]);
    }
}
