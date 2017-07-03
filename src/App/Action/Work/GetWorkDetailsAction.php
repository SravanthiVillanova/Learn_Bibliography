<?php

namespace App\Action\Work;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class GetWorkDetailsAction
{
	private $router;

    private $template;
    
    private $adapter;

    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, Adapter $adapter)
    {
        $this->router   = $router;
        $this->template = $template;
        $this->adapter  = $adapter;
    }
	
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    { 	
	
		if(isset($_GET['autofor']))
		{
			$autofor = $_GET['autofor'];
			if($autofor == 'publisher')
			{
				if(isset($_GET['term']))
				{
					$search_term = $_GET['term'];
					$table = new \App\Db\Table\Publisher($this->adapter);
					$rows = $table->getLikeRecords($search_term);
					foreach ($rows as $i => $row) {
						$rows[$i]['value'] = $row['name'];
						$rows[$i]['label'] = $row['name'];
						$rows[$i]['id'] = $row['id'];
					}
					echo json_encode($rows);
					exit;
					//return new JsonResponse ($this->template->render('app::work::new_work', ['rows' => $rows]));
				}
			}
			if($autofor == 'agent')
			{
				if(isset($_GET['term']))
				{
					$search_term = $_GET['term'];
					$table = new \App\Db\Table\Agent($this->adapter);
					$rows = $table->getLikeRecords($search_term);
					foreach ($rows as $i => $row) {						
						$rows[$i]['id'] = $row['id'];
						$rows[$i]['label'] = $row['fname'];
						$rows[$i]['lname'] = $row['lname'];
						$rows[$i]['alternate_name'] = $row['alternate_name'];
						$rows[$i]['organization_name'] = $row['organization_name'];
					}
					echo json_encode($rows);
					exit;
					//return new JsonResponse ($this->template->render('app::work::new_work', ['rows' => $rows]));
				}
			}
			if($autofor == 'optionlookup')
			{
				if(isset($_GET['term']))
				{
					var_dump($_GET['term']);
					/*$search_term = $_GET['term'];
					$table = new \App\Db\Table\Publisher($this->adapter);
					$rows = $table->getLikeRecords($search_term);
					foreach ($rows as $i => $row) {
						$rows[$i]['value'] = $row['name'];
						$rows[$i]['label'] = $row['name'];
						$rows[$i]['id'] = $row['id'];
					}
					echo json_encode($rows);
					exit;*/
					//return new JsonResponse ($this->template->render('app::work::new_work', ['rows' => $rows]));
				}
			}
		}
		if(isset($_POST['publisher_Id']))
		{
			$pub_id = $_POST['publisher_Id'];
			$table = new \App\Db\Table\PublisherLocation($this->adapter);
			$rows = $table->getPublisherLocations($pub_id);
			foreach ($rows as $i => $row) {
				$rows[$i]['value'] = $row['location'];
				$rows[$i]['label'] = $row['location'];
				$rows[$i]['id'] = $row['id'];
			}
			
			$output = array("publoc" => $rows,);
			echo json_encode($output);
			exit;
		}
		if(isset($_POST['worktype_Id']))
		{
			$wkt_id = $_POST['worktype_Id'];
			$table = new \App\Db\Table\WorkAttribute($this->adapter);
			$paginator = $table->getAttributesForWorkType($wkt_id);
			$itemsCount = $paginator->getTotalItemCount();
			$paginator->setItemCountPerPage($itemsCount);	
			$rows = [];
			foreach ($paginator as $row) :
				$rows[] = $row;
			endforeach;		
			$output = array("worktype_attribute" => $rows,);
			echo json_encode($output);
			exit;
		}
		if(isset($_POST['option']))
		{
			$opt_title = $_POST['option'];
			$wkat_id =  preg_replace("/^\w+:/", "", $_POST['attribute_Id']);
			//$wkat_id = $_POST['attribute_Id'];
			$table = new \App\Db\Table\WorkAttribute_Option($this->adapter);
			$rows = $table->getAttributeOptions($opt_title,$wkat_id);
			/*foreach ($rows as $i => $row) {						
						$rows[$i]['id'] = $row['id'];
						$rows[$i]['label'] = $row['fname'];
						$rows[$i]['lname'] = $row['lname'];
						$rows[$i]['alternate_name'] = $row['alternate_name'];
						$rows[$i]['organization_name'] = $row['organization_name'];
					}*/
			$output = array("attribute_options" => $rows,);
			echo json_encode($output);
			exit;
		}
		if(isset($_POST['folder_Id']))
		{
			$fl_id = $_POST['folder_Id'];
			$table = new \App\Db\Table\Folder($this->adapter);
			$rows = $table->getChild($fl_id);
			$output = array("folder_children" => $rows,);
			echo json_encode($output);
			exit;
		}
		if(isset($_POST['fl_id']))
		{
			$fl_id = $_POST['fl_id'];
			$table = new \App\Db\Table\Folder($this->adapter);
			$src_row = $table->getParentChain($fl_id);
			$output = array("fl_row" => $src_row,);
			echo json_encode($output);
			exit;
		}
		if(isset($_POST['ag_name']))
		{
			$no_of_wks = [];
			$name = $_POST['ag_name'];
			$table = new \App\Db\Table\Agent($this->adapter);
			$ag_row = $table->getLastNameLikeRecords($name);
			foreach($ag_row as $row) :
				$table = new \App\Db\Table\WorkAgent($this->adapter);
				$wks = $table->findRecordByAgentId($row['id']);
				$no_wks = count($wks);
				$no_of_wks[] = $no_wks;
			endforeach;
			for($i=0;$i<count($no_of_wks);$i++)
			{
				$ag_row[$i]['works'] = $no_of_wks[$i];
			}
			$output = array("ag_row" => $ag_row,);
			echo json_encode($output);
			exit;
		}
		if(isset($_POST['ag_id']))
		{
			$ag_id = $_POST['ag_id'];
			$table = new \App\Db\Table\WorkAgent($this->adapter);
			$wks = $table->findRecordByAgentId($ag_id);
			$output = array("ag_no_of_wks" => count($wks),);
			echo json_encode($output);
			exit;
		}
	}
}