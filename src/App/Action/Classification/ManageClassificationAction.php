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
			//manage classification hierarchy
			if($query['action'] == "get_children") {
				$table = new \App\Db\Table\Folder($this->adapter);
				$rows = $table->getChild($query['id']);
				$paginator = new Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($rows));
				return $paginator;
			}
			//view link click
			if($query['action'] == "get_siblings") {
				$table = new \App\Db\Table\Folder($this->adapter);
				$rows = $table->findParent();
				//$paginator = new Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($rows));
				return $rows;
			}
        }
		if (!empty($post['action'])) {
			//add folder
			if ($post['action'] == "new") {
                if ($post['submit'] == "Save") {
					//echo "<pre>";print_r($post);echo "</pre>"; die();
                    $table = new \App\Db\Table\Folder($this->adapter);
                    $table->insertRecords($post['parent_id'], $post['new_classif_engtitle'], $post['new_classif_frenchtitle'],
                                $post['new_classif_germantitle'], $post['new_classif_dutchtitle'], $post['new_classif_spanishtitle'], 
								$post['new_classif_italiantitle'], $post['new_classif_sortorder']);
                }
            }
			//edit folder
            if ($post['action'] == "edit") {
                if ($post['submit'] == "Save") {
                    if (!is_null($post['id'])) {
						//echo "<pre>";print_r($post);echo "</pre>"; die();
                        $table = new \App\Db\Table\Folder($this->adapter);
                        $table->updateRecord($post['id'], $post['edit_texten'], $post['edit_textfr'],
                                            $post['edit_textde'], $post['edit_textnl'], $post['edit_textes'], 
											$post['edit_textit'], $post['edit_sortorder']);
                    }
                }
            }
			//move folder
			if ($post['action'] == "move")
			{
				echo "<pre>";print_r($post);echo "</pre>";die();
				/*$table = new \App\Db\Table\Folder($this->adapter);
				$table->moveFolder($post['id'],$post['fl_to_mv']);*/
				if($post['fl_to_mv'] == "" && $post['fl_parent'] == "")
				{
					echo "case root";//die();
					/*$table = new \App\Db\Table\Folder($this->adapter);
				    $table->moveFolder($post['id'],$post['fl_parent_root']);*/
				}
				else if($post['fl_to_mv'] == "" && $post['fl_parent'] != "")
				{
					echo "case parent";//die();
					/*$table = new \App\Db\Table\Folder($this->adapter);
				    $table->moveFolder($post['id'],$post['fl_parent']);*/
				}
				else
				{
					echo "case last level"; //die();
					/*$table = new \App\Db\Table\Folder($this->adapter);
				    $table->moveFolder($post['id'],$post['fl_to_mv']);*/
				}
				var_dump($post['fl_to_mv']);
				var_dump($post['fl_parent']);
				var_dump($post['fl_parent_root']); die();
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
        $paginator->setDefaultItemCountPerPage(15);
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
			$r = $table->getTrail($query['id'],"");
			$r = $query['fl'] . $r;
			/*$prefix = ':';
			if (substr($r, 0, strlen($prefix)) == $prefix) {
				$r = substr($r, strlen($prefix));
			}*/ 
			//echo $r;
			$ts = explode(":",$r);
			//var_dump($ts);
			$ts = array_reverse($ts);
			//echo "<pre>"; print_r($ts); echo "</pre>"; 
			//die();
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
					'trail' => $ts,
                ]
            )
        );
    }
}
