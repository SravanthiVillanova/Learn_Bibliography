<?php
/**
 * Table Definition for page_instructions.
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

/**
 * Table Definition for page_instructions.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class Page_Instructions extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor.
     */
    public function __construct($adapter)
    {
        parent::__construct('page_instructions', $adapter);
    }

    public function findRecordByPageName($str)
    {
        $rowset = $this->select(array('page_name' => $str));
        $row = $rowset->current();

        return $row;
    }

    public function updateRecord($pg_id, $ins_str)
    {
        $this->update(
            [
                'instructions' => $ins_str,
            ],
            ['id' => $pg_id]
        );
    }

    public function insertRecord($pg_nm, $ins_str)
    {
        $this->insert(
            [
            'page_name' => $pg_nm,
            'instructions' => $ins_str,
            ]
        );
    }
}
