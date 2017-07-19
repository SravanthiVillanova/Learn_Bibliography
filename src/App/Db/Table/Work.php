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
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

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
class Work extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Constructor.
     */
    public function __construct($adapter)
    {
        parent::__construct('work', $adapter);
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
    public function countRecordsByWorkType($id)
    {
        $select = $this->sql->select()->where(['type_id' => $id]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function updateWorkTypeId($id)
    {
        $this->update(
            [
                'type_id' => null,
            ],
            ['type_id' => $id]
        );
    }

    public function findInitialLetter()
    {
        $callback = function ($select) {
            $select->columns(
                [
                    'letter' => new Expression(
                    'DISTINCT(substring(?, 1, 1))',
                    ['title'],
                    [Expression::TYPE_IDENTIFIER]
                    ),
                ]
            );
            $select->order('title');
        };

        return $this->select($callback)->toArray();
    }

    public function findInitialLetterReview()
    {
        $callback = function ($select) {
            $select->columns(
                    [
                        'letter' => new Expression(
                            'DISTINCT(substring(?, 1, 1))',
                            ['title'],
                            [Expression::TYPE_IDENTIFIER]
                        ),
                    ]
                );
            $select->where->equalTo('status', 0);
            $select->order('title');
        };

        return $this->select($callback)->toArray();
    }

    public function findInitialLetterClassify()
    {
        $wid = new Work_Folder($this->adapter);
        $subselect = $wid->getWorkFolder();

        $callback = function ($select) use ($subselect) {
            $select->columns(
                    [
                        'letter' => new Expression(
                            'DISTINCT(substring(?, 1, 1))',
                            ['title'],
                            [Expression::TYPE_IDENTIFIER]
                        ),
                    ]
                );
            $select->where->notIn('id', $subselect);
            $select->order('title');
        };

        return $this->select($callback)->toArray();
    }

    public function displayRecordsByName($letter)
    {
        $select = $this->sql->select();
        $select->where->like('title', $letter.'%');
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function displayReviewRecordsByLetter($letter)
    {
        $select = $this->sql->select();
        $select->where->like('title', $letter.'%');
        $select->where->equalTo('status', 0);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function displayClassifyRecordsByLetter($letter)
    {
        $wid = new Work_Folder($this->adapter);
        $subselect = $wid->getWorkFolder();

        $select = $this->sql->select();
        $select->where->notIn('id', $subselect);
        $select->where->like('title', $letter.'%');

        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function fetchReviewRecords()
    {
        $select = $this->sql->select()->where(['status' => 0]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function fetchClassifyRecords()
    {
        $wid = new Work_Folder($this->adapter);
        $subselect = $wid->getWorkFolder();

        $select = $this->sql->select();
        $select->where->notIn('id', $subselect);

        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function findRecords($title)
    {
        $select = $this->sql->select();
        $select->where->expression('LOWER(title) LIKE ?', strtolower($title).'%');
        //->where(['name' => $name]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    public function findRecordById($id)
    {
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        return $row;
    }

    public function insertRecords($type_id, $title, $subtitle, $paralleltitle, $description, $create_date, $create_user_id, $status, $pub_yrFrom)
    {
        $this->insert(
            [
            'work_id' => null,
            'type_id' => $type_id,
            'title' => $title,
            'subtitle' => $subtitle,
            'paralleltitle' => $paralleltitle,
            'description' => $description,
            'create_date' => $create_date,
            'create_user_id' => $create_user_id,
            'modify_date' => '0000-00-00 00:00:00',
            'modify_user_id' => null,
            'status' => $status,
            'publish_year' => $pub_yrFrom,
            'publish_month' => null,
            ]
        );
        $id = $this->getLastInsertValue();

        return $id;
    }

    public function deleteRecordByWorkId($id)
    {
        $this->delete(['id' => $id]);
    }

    public function updateRecords($id, $type_id, $title, $subtitle, $paralleltitle, $desc, $modify_date, $modify_user, $status, $pub_yrFrom)
    {
        $this->update(
            [
            'work_id' => null,
            'type_id' => $type_id,
            'title' => $title,
            'subtitle' => $subtitle,
            'paralleltitle' => $paralleltitle,
            'description' => $desc,
            'modify_date' => $modify_date,
            'modify_user_id' => $modify_user,
            'status' => $status,
            'publish_year' => $pub_yrFrom,
            'publish_month' => null,
            ],
            ['id' => $id]
        );
    }
	
	public function getPendingReviewWorksCount()
    {
        $callback = function ($select) {
            $select->columns(
                    [
                        'review_count' => new Expression(
                            'Count(?)',
                            ['*'],
                            [Expression::TYPE_IDENTIFIER]
                        ),
                    ]
                );
            $select->where->equalTo('status', 0);
        };

        return $this->select($callback)->toArray();
    }
}
