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
		if(isset($_POST['publisher_Id']))
		{
			//$ddata = array();
			//die($_POST['publisher_Id']);
			$pub_id = $_POST['publisher_Id'];
			$table = new \App\Db\Table\PublisherLocation($this->adapter);
			$rows = $table->getPublisherLocations($pub_id);
			foreach ($rows as $i => $row) {
				$rows[$i]['value'] = $row['location'];
				$rows[$i]['label'] = $row['location'];
				$rows[$i]['id'] = $row['id'];
				//$ddata[] = $rows;
			}
			
			$output = array("publoc" => $rows,);
			//var_dump($rows); die();
			echo json_encode($output);
		}
	}
}