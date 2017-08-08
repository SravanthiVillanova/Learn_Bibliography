<?php
/**
 * Table Definition for translate_language.
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

/**
 * Table Definition for translate_language.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class TranslateLanguage extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor.
     */
    public function __construct($adapter)
    {
        parent::__construct('translate_language', $adapter);
    }
    
    public function insertRecords($de1, $en1, $es1, $fr1, $it1, $nl1)
    {
        $this->insert(
            [
            'text_de' => $de1,
            'text_en' => $en1,
            'text_es' => $es1,
            'text_fr' => $fr1,
            'text_it' => $it1,
            'text_nl' => $nl1,
            ]
        );
    }

    public function updateRecord($id, $de1, $en1, $es1, $fr1, $it1, $nl1)
    {
        $this->update(
            [
                'text_de' => $de1,
                'text_en' => $en1,
                'text_es' => $es1,
                'text_fr' => $fr1,
                'text_it' => $it1,
                'text_nl' => $nl1, ],
            ['id' => $id]
        );
    }

    public function deleteRecord($id)
    {
        //echo $id;
        $this->delete(['id' => $id]);
        //$this->tableGateway->delete(['id' => $id]);
    }

    public function findRecordById($id)
    {
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        return $row;
    }
}
