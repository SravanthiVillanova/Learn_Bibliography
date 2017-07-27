<?php

namespace App\Action\Publisher;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;

class ManagePublisherAction
{
    private $router;

    private $template;

    private $adapter;

    //private $dbh;
    //private $qstmt;

    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, Adapter $adapter)
    {
        $this->router = $router;
        $this->template = $template;
        $this->adapter = $adapter;
    }

	protected function searchAgent($params)
	{
		// search by name
        if (!empty($params['name'])) {
            $table = new \App\Db\Table\Publisher($this->adapter);

            return $table->findRecords($params['name']);
        }
        // search by location
        if (!empty($params['location'])) {
            $table = new \App\Db\Table\PublisherLocation($this->adapter);

            return $table->findRecords($params['location']);
        }
        // search by letter
        if (!empty($params['letter'])) {
            $table = new \App\Db\Table\Publisher($this->adapter);

            return $table->displayRecordsByName($params['letter']);
        }
	}
	
	protected function doDelete($post)
	{
		$locs = [];
		if (!is_null($post['id'])) {
                        $table = new \App\Db\Table\WorkPublisher($this->adapter);
                        $table->deleteRecordByPub($post['id']);

                        $table = new \App\Db\Table\PublisherLocation($this->adapter);
                        $table->deletePublisherRecord($post['id'], $locs);

                        $table = new \App\Db\Table\Publisher($this->adapter);
                        $table->deleteRecord($post['id']);
                    }
	}
	
	protected function doMerge($post)
	{
		//$table = new \App\Db\Table\Publisher($this->adapter);
        //return $table->findRecords($post['source_publisher']);
		echo "<pre>"; print_r($post); echo "</pre>"; die();
		foreach ($_POST['source_location_id'] as $sourceId => $action) {
        if ($action == 'move') {
            $sql = "UPDATE work_publisher SET publisher_id = '" . $_POST['end_publisher_id'] . "' WHERE publisher_id = '" . $_POST['source_publisher_id'] . "' AND location_id = '$sourceId'";
            $do->query($sql);
            $sql = "UPDATE publisher_location SET publisher_id = '" . $_POST['end_publisher_id'] . "' WHERE publisher_id = '" . $_POST['source_publisher_id'] . "' AND id = '$sourceId'";
            $do->query($sql);
        } elseif ($action == 'merge') {
            $sql = "UPDATE work_publisher SET publisher_id = '" . $_POST['end_publisher_id'] . "', location_id = '" . $_POST['merge_publisher_id'] . "' WHERE publisher_id = '" . $_POST['source_publisher_id'] . "' AND location_id = '$sourceId'";
            $do->query($sql);
            $sql = "DELETE FROM publisher_location WHERE id = '$sourceId'";
            $do->query($sql);
        }
		}
		foreach ($post['src_loc'] as $source_locid => $action) :
			if($action == 'move') {
				//update workpub set pubid=destpubid where pubid=srcpubid and locid = $source_locid
				$table = new \App\Db\Table\WorkPublisher($this->adapter);
				$table->movePublisher($post['mrg_src_id'], $post['mrg_dest_id'], $source_locid);
				//update publoc set pubid = destpubid where pubid=srcpubid and id=$source_locid
				$table = new \App\Db\Table\PublisherLocation($this->adapter);
				$table->movePublisher($post['mrg_src_id'], $post['mrg_dest_id'], $source_locid);
			}
			elseif ($action == 'merge') {
				//update workpub set pubid=destpubid and locid=mrgpublocid where pubid=srcpubid and locid=$source_locid
				$table = new \App\Db\Table\WorkPublisher($this->adapter);
				$table->mergePublisher($post['mrg_src_id'], $post['mrg_dest_id'], $source_locid, $post['dest_loc_select']);
				//delete $source_locid from publoc
				$table = new \App\Db\Table\PublisherLocation($this->adapter);
				$table->mergePublisher($source_locid);
			}
		endforeach;
	}
	protected function doAction($post)
	{
		//add a new publisher
            if ($post['action'] == 'new') {
                if ($post['submitt'] == 'Save') {
                    $table = new \App\Db\Table\Publisher($this->adapter);
                    $table->insertRecords($post['name_publisher']);
                }
            }
            //edit a publisher
            if ($post['action'] == 'edit') {
                if ($post['submitt'] == 'Save') {
                    if (!is_null($post['id'])) {
                        $table = new \App\Db\Table\Publisher($this->adapter);
                        $table->updateRecord($_POST['id'], $_POST['publisher_newname']);
                    }
                }
            }
            //delete a publisher */
            if ($post['action'] == 'delete') {
                if ($post['submitt'] == 'Delete') {
                    $this->doDelete($post);
                }
            } 
			if ($post['action'] == 'merge_publisher') {
                if ($post['submit_save'] == 'Save') {
                    $this->doMerge($post);
                }
            }
	}
	
    protected function getPaginator($params, $post)
    {
		//search
		if (!empty($params)) {
			return ($this->searchAgent($params));
		}
		      
        //edit, delete actions on publisher
        if (!empty($post['action'])) {            
			   //add edit delete merge publisher
                $this->doAction($post);
			
            //Cancel edit\delete
			if (isset($post['submitt'])) {
				if ($post['submitt'] == 'Cancel') {
					$table = new \App\Db\Table\Publisher($this->adapter);

					return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
				}
			}
        }
        // default: blank/missing search
        $table = new \App\Db\Table\Publisher($this->adapter);

        return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
    }

	protected function getSearchParams($query)
	{
		$searchParams = [];
        if (!empty($query['name'])) {
            $searchParams[] = 'name='.urlencode($query['name']);
        }
        if (!empty($query['location'])) {
            $searchParams[] = 'location='.urlencode($query['location']);
        }
        if (!empty($query['letter'])) {
            $searchParams[] = 'letter='.urlencode($query['letter']);
        }
	}
	
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $table = new \App\Db\Table\Publisher($this->adapter);
        $characs = $table->findInitialLetter();

        $query = $request->getqueryParams();
        $post = [];
        if ($request->getMethod() == 'POST') {
            $post = $request->getParsedBody();
        }

        $paginator = $this->getPaginator($query, $post);
        $paginator->setDefaultItemCountPerPage(7);
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

        $searchParams = $this->getSearchParams($query);
		
		if (!is_null($searchParams)) {
		    $searchParams = implode('&', $searchParams);
		}
		else
		{
			$searchParams = '';
		}
		
        if (isset($post['action'])) {
            if ($post['action'] == 'merge_publisher') {
                return new HtmlResponse(
                $this->template->render(
                'app::publisher::merge_publisher',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    'searchParams' => $searchParams,
                    'adapter' => $this->adapter,
                ]
            )
            );
            }
            if ($post['action'] == 'new' || $post['action'] == 'edit' || $post['action'] == 'delete') {
                return new HtmlResponse(
                $this->template->render(
                'app::publisher::manage_publisher',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    'searchParams' => implode('&', $searchParams),
                    'carat' => $characs,
                ]
            )
            );
            }
        } else {
            return new HtmlResponse(
            $this->template->render(
                'app::publisher::manage_publisher',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    'searchParams' => $searchParams,
                    'carat' => $characs,
                ]
            )
        );
        }
    }
}
