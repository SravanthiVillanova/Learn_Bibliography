<?php

namespace App\Action\Work;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class ManageWorkAction
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

    /*public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        return new HtmlResponse($this->template->render('app::work::manage_work', ['rows' => $rows]));
    }*/
	
	protected function getPaginator($params, $post)
    {
        // search by letter
        if (!empty($params['letter'])) {
			//review records
			if($params['action'] == 'review') {
				$table = new \App\Db\Table\Work($this->adapter);
				return $table->displayReviewRecordsByLetter($params['letter']);
			}
			//classify records
			else if($params['action'] == 'classify') {
				$table = new \App\Db\Table\Work($this->adapter);
				return $table->displayClassifyRecordsByLetter($params['letter']);
				
			}
			else {
				$table = new \App\Db\Table\Work($this->adapter);
				return $table->displayRecordsByName($params['letter']);
			}
        }
		// Work Lookup
        if (!empty($params['find_worktitle'])) {
			//echo "name is " . $params['find_worktitle'];
            $table = new \App\Db\Table\Work($this->adapter);
            $paginator = $table->findRecords($params['find_worktitle']);
			return $paginator;
        }
		if(!empty($params['action'])) {
			//Display works which need review
			if ($params['action'] == "review") {
				$table = new \App\Db\Table\Work($this->adapter);
				$paginator = $table->fetchReviewRecords();
				return $paginator;
			}
			//Display works which are to be classified under folders
			if($params['action'] == "classify") {
				$table = new \App\Db\Table\Work($this->adapter);
				$paginator = $table->fetchClassifyRecords();
				return $paginator;
			}
		}
		/*if(isset($post['get_parent']))
		{
			var_dump("selected is " . $post['get_parent']);
			$table = new \App\Db\Table\Folder($this->adapter);
			$rows = $table->getChild($post['get_parent']);
			return $rows;
		}*/
        //Cancel edit\delete
        if ($post['submit_cancel'] == "Cancel") {
            $table = new \App\Db\Table\Work($this->adapter);
            return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
        }
        // default: blank/missing search
        $table = new \App\Db\Table\Work($this->adapter);
        return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {  
		//var_dump("helloo");// die();
		$post = [];
        if ($request->getMethod() == "POST") {
            $post = $request->getParsedBody();
        }
        
		if(isset($post['get_parent']))
		{
			//var_dump("selected is " . $post['get_parent']);
			$table = new \App\Db\Table\Folder($this->adapter);
			$rows = $table->getChild($post['get_parent']);
			//$rc = json_encode($rows);
			//exit;
			
			return new HtmlResponse(
            $this->template->render(
                'app::work::get_work_details',
                [					
                    'rows' => $rows,
					'request' => $request, 
					'adapter' => $this->adapter,
                ]
            )
			);
			//var_dump($rows);
			//print_r(json_encode($rows));
			//return $rows;
			
		}
        $query = $request->getqueryParams();
		if($query['action'] == 'review') {
			$table = new \App\Db\Table\Work($this->adapter);
			$characs = $table->findInitialLetterReview();
		} 
		else if ($query['action'] == 'classify') {
			$table = new \App\Db\Table\Work($this->adapter);
			$characs = $table->findInitialLetterClassify();
		}
		else 
		{
			$table = new \App\Db\Table\Work($this->adapter);
			$characs = $table->findInitialLetter();
		}
        
		
        $paginator = $this->getPaginator($query, $post);
        $paginator->setDefaultItemCountPerPage(20);		
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

        $searchParams = [];
        if (!empty($query['find_worktitle'])) {
            $searchParams[] = 'find_worktitle=' . urlencode($query['find_worktitle']);
        }
        if (!empty($query['letter']) && $query['action'] == 'alphasearch') {			
				$searchParams[] = 'letter=' . urlencode($query['letter']);	
        }
		if ($query['action'] == 'review') {
			if(!empty($query['letter'])) {
				$searchParams[] = 'action=' . urlencode($query['action']) . '&letter=' .urlencode($query['letter']);
			}
			else {
				$searchParams[] = 'action=' . urlencode($query['action']);
			}
        }
		if ($query['action'] == 'classify') {
			if(!empty($query['letter'])) {
				$searchParams[] = 'action=' . urlencode($query['action']) . '&letter=' .urlencode($query['letter']);
			}
			else {
				$searchParams[] = 'action=' . urlencode($query['action']);
			}
        }

       if ($query['action'] == "review") {
		   echo "entered if";
            return new HtmlResponse(
            $this->template->render(
                'app::work::review_work',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    'searchParams' => implode('&', $searchParams),
                    'carat' => $characs,
					'request' => $request, 
					'adapter' => $this->adapter,
                ]
            )
			);
        } 
		else if ($query['action'] == "classify") {
			echo "entered else if";
			return new HtmlResponse(
            $this->template->render(
                'app::work::classify_work',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    'searchParams' => implode('&', $searchParams),
                    'carat' => $characs,
					'request' => $request, 
					'adapter' => $this->adapter,
                ]
            )
			);
		}
		else { 
		echo "entered else";
            return new HtmlResponse(
            $this->template->render(
                'app::work::manage_work',
                [					
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    'searchParams' => implode('&', $searchParams),
                    'carat' => $characs,
					'request' => $request, 
					'adapter' => $this->adapter,
                ]
            )
			);
		}
	}
}
