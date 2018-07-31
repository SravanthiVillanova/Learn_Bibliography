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
namespace VuBib\Db\Table;

use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Sql;

/**
 * Table Definition for attribute_option_subattribute.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class Attribute_Option_SubAttribute extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * WorkAttribute constructor.
     *
     * @param Adapter $adapter for db connection
     */
    public function __construct($adapter)
    {
        parent::__construct('attribute_option_subattribute', $adapter);
    }
    
    /**
     * Find record using workattribute id
     *
     * @param Integer $opt_id     workattribute option id
     * @param Integer $subattr_id sub attribute id
     *
     * @return Array $row sub atrribute
     */
    public function findRecordByOption($opt_id, $subattr_id)
    {
        $rowset = $this->select(array('subattribute_id' => $subattr_id, 'option_id' => $opt_id));
        $row = $rowset->current();
        
        return $row;
    }
    
    /**
     * Add record
     *
     * @param string $field work attribute name
     * @param string $type  work attribute type
     *
     * @return id newly inserted subattribute id
     */
    public function insertRecord($wkat_id, $opt_id, $subattr_id, $subattr_val = "")
    {
        $this->insert(
            [
            'workattribute_id' => $wkat_id,
            'option_id' => $opt_id,
            'subattribute_id' => $subattr_id,
            'subattr_value' => $subattr_val,
            ]
        );
    }
}