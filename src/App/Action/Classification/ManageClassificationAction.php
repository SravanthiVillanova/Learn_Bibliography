<?php

namespace App\Action\Classification;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect;

class ManageClassificationAction
{
    private $router;

    private $template;
    
    private $adapter;
    
    
    //private $dbh;
    //private $qstmt;

    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, Adapter $adapter)
    {
        $this->router   = $router;
        $this->template = $template;
        $this->adapter  = $adapter;
    }
    
    protected function getPaginator($query, $post)
    {
        if (!empty($query['action'])) {
            //export classification
            if ($query['action'] == "export_classification") {
                echo "export folder";
                /*if ($post['submitt'] == "Save") {
                    $table = new \App\Db\Table\WorkType($this->adapter);
                    $table->insertRecords($post['new_worktype']);
                }*/
            }
			//manage classification hierarchy
			if($query['action'] == "get_children") {
				$table = new \App\Db\Table\Folder($this->adapter);
				$rows = $table->getChild($query['id']);
				$paginator = new Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($rows));
				return $paginator;
			}
        }
        // default: blank for listing in manage
        $table = new \App\Db\Table\Folder($this->adapter);
        $paginator = $table->findParent();
        return $paginator;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $query = $request->getqueryParams();
        //print_r($query);
        $post = [];
        if ($request->getMethod() == "POST") {
            $post = $request->getParsedBody();
        }
        $paginator = $this->getPaginator($query, $post);
        $paginator->setDefaultItemCountPerPage(7);
        $allItems = $paginator->getTotalItemCount();
        $countPages = $paginator->count();
        
        $currentPage = isset($query['page']) ? $query['page'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        $paginator->setCurrentPageNumber($currentPage);

        if ($currentPage == $countPages) {
            $next = $currentPage;
            $previous = $currentPage - 1;
        } elseif ($currentPage == 1) {
            $next = $currentPage + 1;
            $previous = 1;
        } else {
            $next = $currentPage + 1;
            $previous = $currentPage - 1;
        }
		//gel folder name for bread crumb
		if($query['action'] == "get_children") {
			$table= new \App\Db\Table\Folder($this->adapter);
			//$previous_folder = $table->getParent($query['id']);
			$ts = $table->getTrail(3);
			echo "<pre>"; print_r($ts); echo "</pre>"; die();
		}
        return new HtmlResponse(
            $this->template->render(
                'app::classification::manage_classification',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
					'previous_folder' => $previous_folder,
                ]
            )
        );
    }
}
