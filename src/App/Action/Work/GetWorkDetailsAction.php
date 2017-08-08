<?php
/**
 * ISBN validation and conversion functionality
 *
 * PHP version 5
 *
 * Copyright (c) Falvey Library 2017.
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
 * @link     https:// Main Page
 */
namespace App\Action\Work;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;

/**
 * Class Definition for GetWorkDetailsAction.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class GetWorkDetailsAction
{
    private $router;

    private $template;

    private $adapter;

	/**
     * GetWorkDetailsAction constructor.
     *
     * @param Router\RouterInterface                  $router
     * @param Template\TemplateRendererInterface|null $template
     * @param Adapter             					  $adapter
     */
    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, Adapter $adapter)
    {
        $this->router = $router;
        $this->template = $template;
        $this->adapter = $adapter;
    }

    protected function pubName($post)
    {
        $no_of_wks = [];
        $name = $post['pub_name'];
        $table = new \App\Db\Table\Publisher($this->adapter);
        $publisher_row = $table->findRecords($name);
        foreach ($publisher_row as $row) :
                $pub_row[] = $row;
        endforeach;
        foreach ($pub_row as $row) :
                $table = new \App\Db\Table\WorkPublisher($this->adapter);
        $wks = $table->findRecordByPublisherId($row['id']);
        $no_wks = count($wks);
        $no_of_wks[] = $no_wks;
        endforeach;
        for ($i = 0; $i < count($no_of_wks); ++$i) {
            $pub_row[$i]['works'] = $no_of_wks[$i];
        }
        $output = array('pub_row' => $pub_row);
        echo json_encode($output);
        exit;
    }
    
    protected function publisherIdLocs($post)
    {
        $pub_id = $post['publisher_Id_locs'];
        $table = new \App\Db\Table\PublisherLocation($this->adapter);
        $pub_loc_rows = $table->getPublisherLocations($pub_id);
        foreach ($pub_loc_rows as $i => $row) {
            $pub_loc_rows[$i]['value'] = $row['location'];
            $pub_loc_rows[$i]['label'] = $row['location'];
            $pub_loc_rows[$i]['id'] = $row['id'];
        }
        foreach ($pub_loc_rows as $row) :
                $table = new \App\Db\Table\WorkPublisher($this->adapter);
        $wks = $table->findRecordByLocationId($row['id']);
        $no_wks = count($wks);
        $no_of_wks[] = $no_wks;
        endforeach;
        for ($i = 0; $i < count($no_of_wks); ++$i) {
            $pub_loc_rows[$i]['works'] = $no_of_wks[$i];
        }
        $output = array('pub_locs' => $pub_loc_rows);
        echo json_encode($output);
        exit;
    }
    
    protected function agName($post)
    {
        $no_of_wks = [];
        $name = $post['ag_name'];
        $table = new \App\Db\Table\Agent($this->adapter);
        $ag_row = $table->getLastNameLikeRecords($name);
        foreach ($ag_row as $row) :
                $table = new \App\Db\Table\WorkAgent($this->adapter);
        $wks = $table->findRecordByAgentId($row['id']);
        $no_wks = count($wks);
        $no_of_wks[] = $no_wks;
        endforeach;
        for ($i = 0; $i < count($no_of_wks); ++$i) {
            $ag_row[$i]['works'] = $no_of_wks[$i];
        }
        $output = array('ag_row' => $ag_row);
        echo json_encode($output);
        exit;
    }
    
    protected function insText($post)
    {
        $ins_str = $post['ins_text'];
        if (!empty($post['pg_id'])) {
            $pg_id = $post['pg_id'];
            $table = new \App\Db\Table\Page_Instructions($this->adapter);
            $table->updateRecord($pg_id, $ins_str);
        } else {
            $pg_nm = $post['pg_nm'];
            $table = new \App\Db\Table\Page_Instructions($this->adapter);
            $table->insertRecord($pg_nm, $ins_str);
        }
        exit;
    }
    
    protected function folderId($post)
    {
        $fl_id = $post['folder_Id'];
        $table = new \App\Db\Table\Folder($this->adapter);
        $rows = $table->getChild($fl_id);
        $output = array('folder_children' => $rows);
        echo json_encode($output);
        exit;
    }
    
    protected function flId($post)
    {
        $fl_id = $post['fl_id'];
        $table = new \App\Db\Table\Folder($this->adapter);
        $src_row = $table->getParentChain($fl_id);
        $output = array('fl_row' => $src_row);
        echo json_encode($output);
        exit;
    }
    
    protected function option($post)
    {
        $opt_title = $post['option'];
        $wkat_id = preg_replace("/^\w+:/", '', $post['attribute_Id']);
            //$wkat_id = $_POST['attribute_Id'];
            $table = new \App\Db\Table\WorkAttribute_Option($this->adapter);
        $rows = $table->getAttributeOptions($opt_title, $wkat_id);
        $output = array('attribute_options' => $rows);
        echo json_encode($output);
        exit;
    }
    
    protected function worktypeId($post)
    {
        $wkt_id = $post['worktype_Id'];
        $table = new \App\Db\Table\WorkAttribute($this->adapter);
        $paginator = $table->getAttributesForWorkType($wkt_id);
        $itemsCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($itemsCount);
        $rows = [];
        foreach ($paginator as $row) :
                $rows[] = $row;
        endforeach;
        $output = array('worktype_attribute' => $rows);
        echo json_encode($output);
        exit;
    }
    
    protected function publisherId($post)
    {
        $pub_id = $post['publisher_Id'];
        $table = new \App\Db\Table\PublisherLocation($this->adapter);
        $rows = $table->getPublisherLocations($pub_id);
        foreach ($rows as $i => $row) {
            $rows[$i]['value'] = $row['location'];
            $rows[$i]['label'] = $row['location'];
            $rows[$i]['id'] = $row['id'];
        }
        $output = array('publoc' => $rows);
        echo json_encode($output);
        exit;
    }
    
    protected function agId($post)
    {
        $ag_id = $post['ag_id'];
        $table = new \App\Db\Table\WorkAgent($this->adapter);
        $wks = $table->findRecordByAgentId($ag_id);
        $output = array('ag_no_of_wks' => count($wks));
        echo json_encode($output);
        exit;
    }
    
    protected function getAutoSuggest($autofor, $search_term)
    {
        if ($autofor == 'publisher') {
            $table = new \App\Db\Table\Publisher($this->adapter);
            $rows = $table->getLikeRecords($search_term);
            foreach ($rows as $i => $row) {
                $rows[$i]['value'] = $row['name'];
                $rows[$i]['label'] = $row['name'];
                $rows[$i]['id'] = $row['id'];
            }
            return $rows;
        }
        if ($autofor == 'agent') {
            $table = new \App\Db\Table\Agent($this->adapter);
            $rows = $table->getLikeRecords($search_term);
            foreach ($rows as $i => $row) {
                $rows[$i]['id'] = $row['id'];
                $rows[$i]['label'] = $row['fname'];
                $rows[$i]['lname'] = $row['lname'];
                $rows[$i]['alternate_name'] = $row['alternate_name'];
                $rows[$i]['organization_name'] = $row['organization_name'];
            }
            return $rows;
        }
    }
    
    public function doPost($post)
    {
        if (isset($post['publisher_Id'])) {
            $this->publisherId($post);
        }
        if (isset($post['pub_name'])) {
            $this->pubName($post);
        }
        if (isset($post['publisher_Id_locs'])) {
            $this->publisherIdLocs($post);
        }
        if (isset($post['worktype_Id'])) {
            $this->worktypeId($post);
        }
        if (isset($post['option'])) {
            $this->option($post);
        }
        if (isset($post['folder_Id'])) {
            $this->folderId($post);
        }
        if (isset($post['fl_id'])) {
            $this->flId($post);
        }
        if (isset($post['ag_name'])) {
            $this->agName($post);
        }
        if (isset($post['ag_id'])) {
            $this->agId($post);
        }
        if (isset($post['ins_text'])) {
            $this->insText($post);
        }
    }
	
	/**
	* invokes required template
	**/
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        if (isset($_GET['autofor'])) {
            $autofor = $_GET['autofor'];
            if (isset($_GET['term'])) {
                $search_term = $_GET['term'];
                $rows = $this->getAutoSuggest($autofor, $search_term);
            }
            echo json_encode($rows);
            exit;
        }
        
        $post = [];
        if ($request->getMethod() == 'POST') {
            $post = $request->getParsedBody();
            $this->doPost($post);
        }
    }
}
