<?php
/**
 * Table Definition for folder.
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
namespace App\Db\Table;

use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

/**
 * Table Definition for folder.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class Folder extends \Zend\Db\TableGateway\TableGateway
{
    /**
     * Folder constructor.
     *
     * @param Adapter $adapter for db connection
     */
    public function __construct($adapter)
    {
        parent::__construct('folder', $adapter);
    }

    /**
     * Find folders with no parent.
     *
     * @return Paginator $paginatorAdapter folder records as paginator
     */
    public function findParent()
    {
        $select = $this->sql->select()->where(['parent_id' => null]);
        $paginatorAdapter = new DbSelect($select, $this->adapter);

        return new Paginator($paginatorAdapter);
    }

    /**
     * Export folders in a hierarchial way to a csv file.
     *
     * @return empty
     */
    public function exportClassification()
    {
        $fl = new self($this->adapter);
        $callback = function ($select) {
            $select->columns(['*']);
            $select->where('parent_id IS NULL');
        };
        $row = $this->select($callback)->toArray();
        $escaper = new \Zend\Escaper\Escaper('utf-8');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=test_export.csv');
        $file = fopen('php://output', 'w') or die('Unable to open file!');
        //add BOM to fix UTF-8 in Excel
        fputs($file, $bom = (chr(0xEF).chr(0xBB).chr(0xBF)));
        foreach ($row as $t):
            $content = $t['id'].' '.$escaper->escapeHtml($t['text_fr']).' ';
        fputcsv($file, array($content));
        $fl->getDepth($t['id'], $file, $content);
        endforeach;
        fflush($file);
        fclose($file);
        exit;
    }

    /**
     * Get the depth of each folder and write it to a file.
     *
     * @param Number $id      id of the folder
     * @param string $file    file to which folder hierarchy is to be written
     * @param string $content content to be written to file
     *
     * @return empty
     */
    public function getDepth($id, $file, $content)
    {
        $fl = new self($this->adapter);
        $escaper = new \Zend\Escaper\Escaper('utf-8');
        $con = $content;
        $current_parent_id = $id;
        $callback = function ($select) use ($current_parent_id) {
            $select->columns(['*']);
            $select->where->equalTo('parent_id', $current_parent_id);
        };
        $rc = $this->select($callback)->toArray();
        if (count($rc) != 0) {
            for ($i = 0; $i < count($rc); ++$i) {
                $con1 = ' '.$escaper->escapeHtml($rc[$i]['text_fr']).' ';
                fputcsv($file, array($con.$con1));
                $current_parent_id = $rc[$i]['id'];
                $fl->getDepth($current_parent_id, $file, $con.$con1);
            }
        }
    }

    /**
     * Get children of a parent folder.
     *
     * @param Number $parent parent id of a folder
     *
     * @return Array $rows folder child records
     */
    public function getChild($parent)
    {
        $callback = function ($select) use ($parent) {
            $select->columns(['*']);
            $select->where->equalTo('parent_id', $parent);
            //$select->order(new Expression('case when sort_order is null then 1 else 0 end, sort_order'),'text_fr');
            $select->order('sort_order');
            $select->order('text_fr');
        };
        $rows = $this->select($callback)->toArray();
        return $rows;
    }

    /**
     * Get parent of a folder.
     *
     * @param Number $child id of a folder
     *
     * @return Array $row folder parent record
     */
    public function getParent($child)
    {
        $rowset = $this->select(array('id' => $child));
        $row = $rowset->current();

        return $row;
    }

    /**
     * Get folders with no parent.
     *
     * @return Array $rows folder records with no parent
     */
    public function getFoldersWithNullParent()
    {
        $callback = function ($select) {
            $select->columns(['*']);
            $select->where('parent_id IS NULL');
        };
        $rows = $this->select($callback)->toArray();

        return $rows;
    }

    /**
     * Get the hierarchial trail of a folder.
     *
     * @param Number $id id of the folder
     * @param string $r  string to convert array
     *
     * @return string $r hierarchial trail of folder as a string
     */
    public function getTrail($id, $r)
    {
        $str = '';
        $str = $r;
        $fl = new self($this->adapter);
        $callback = function ($select) use ($id) {
            $select->columns(['*']);
            $select->join(
                ['b' => 'folder'], 'folder.id = b.parent_id',
                ['parent_id']
            );
            $select->where->equalTo('b.id', $id);
        };
        $rc = $this->select($callback)->toArray();
        if (count($rc) == 0) {
            return $str.':'.'Top';
        } else {
            $r = $r.':'.$rc[0]['text_fr'].'*'.$rc[0]['id'];
            $r = $fl->getTrail($rc[0]['parent_id'], $r);
            return $r;
        }
    }

    /**
     * Find folder record.
     *
     * @param Number $id id of folder
     *
     * @return Array $row folder record
     */
    public function findRecordById($id)
    {
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();

        return $row;
    }

    /**
     * Get the hierarchial parent chain for a folder.
     *
     * @param Number $id id of the folder
     *
     * @return Array $encounteredIds parent hierarchy of a folder
     */
    public function getParentChain($id)
    {
        $fl = new self($this->adapter);
        $row = $fl->getParent($id);

        $encounteredIds = array($row['id']);
        $current = $row['parent_id'];

        while ($current != null && !in_array($current, $encounteredIds)) {
            $row = $fl->getParent($current);

            $encounteredIds[] = $row['id'];
            $current = $row['parent_id'];
        }

        $encounteredIds = array_reverse($encounteredIds);

        return $encounteredIds;
    }

    /**
     * Get the hierarchial parent chain record for a folder.
     *
     * @param Number $id id of the folder
     *
     * @return Array $parentList parent hierarchy of a folder
     */
    public function getParentChainRecord($id, $reverse = false)
    {
		$parentList = array();
        $fl = new self($this->adapter);
        $row = $fl->getParent($id);

        $encounteredIds = array($row['id']);
        $current = $row['parent_id'];

        while ($current != null && !in_array($current, $encounteredIds)) {
            $row = $fl->getParent($current);

            $encounteredIds[] = $row['id'];
			$parentList[] = $row;
            $current = $row['parent_id'];
        }

		if ($reverse) {
            $parentList = array_reverse($parentList);
		}

        return $parentList;
    }	

    /**
     * Insert folder record.
     *
     * @param Number $parent_id  id of parent of folder
     * @param String $text_en    english name of folder
     * @param String $text_fr    french name of folder
     * @param String $text_de    german name of folder
     * @param String $text_nl    dutch name of folder
     * @param String $text_es    spanish name of folder
     * @param String $text_it    italian name of folder
     * @param Number $sort_order order of the folder among its siblings
     *
     * @return empty
     */
    public function insertRecords($parent_id, $text_en, $text_fr, $text_de, $text_nl, $text_es, $text_it, $sort_order)
    {
        $this->insert(
            [
                'parent_id' => $parent_id,
                'text_en' => $text_en,
                'text_fr' => $text_fr,
                'text_de' => $text_de,
                'text_nl' => $text_nl,
                'text_es' => $text_es,
                'text_it' => $text_it,
                'sort_order' => $sort_order,
            ]
        );
    }

    /**
     * Update folder record.
     *
     * @param Number $id         id of folder
     * @param String $text_en    english name of folder
     * @param String $text_fr    french name of folder
     * @param String $text_de    german name of folder
     * @param String $text_nl    dutch name of folder
     * @param String $text_es    spanish name of folder
     * @param String $text_it    italian name of folder
     * @param Number $sort_order order of the folder among its siblings
     *
     * @return empty
     */
    public function updateRecord($id, $text_en, $text_fr, $text_de, $text_nl, $text_es, $text_it, $sort_order)
    {
        $this->update(
            [
                'text_en' => $text_en,
                'text_fr' => $text_fr,
                'text_de' => $text_de,
                'text_nl' => $text_nl,
                'text_es' => $text_es,
                'text_it' => $text_it,
                'sort_order' => $sort_order,
            ],
            ['id' => $id]
        );
    }

    /**
     * Move folder.
     *
     * @param Number $id        id of folder
     * @param Number $parent_id parent id folder
     *
     * @return empty
     */
    public function moveFolder($id, $parent_id)
    {
        $this->update(
            [
                'parent_id' => $parent_id,
            ],
            ['id' => $id]
        );
    }

    /**
     * Merge folder.
     *
     * @param Number $sid id of folder
     * @param Number $did id folder
     *
     * @return empty
     */
    public function mergeFolder($sid, $did)
    {
        $this->update(
            [
                'parent_id' => $did,
            ],
            ['parent_id' => $sid]
        );
    }

    /**
     * Delete folder.
     *
     * @param Number $sid id of folder
     *
     * @return empty
     */
    public function mergeDelete($sid)
    {
        $this->delete(['id' => $sid]);
    }

    /**
     * Get siblings of a folder.
     *
     * @param Number $pid parent id of the folder
     *
     * @return Array $rows folder records
     */
    public function getSiblings($pid)
    {
        if (is_null($pid)) {
            $callback = function ($select) {
                $select->columns(['*']);
                $select->where('parent_id IS NULL');
            };
        } else {
            $callback = function ($select) use ($pid) {
                $select->columns(['*']);
                $select->where->equalTo('parent_id', $pid);
            };
        }
        $rows = $this->select($callback)->toArray();

        return $rows;
    }
}
