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

    protected function getPaginator($params, $post)
    {
        $locs = [];
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
        //edit, delete actions on publisher
        if (!empty($post['action'])) {
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
                    if (!is_null($post['id'])) {
                        $table = new \App\Db\Table\WorkPublisher($this->adapter);
                        $table->deleteRecordByPub($post['id']);

                        $table = new \App\Db\Table\PublisherLocation($this->adapter);
                        $table->deletePublisherRecord($post['id'], $locs);

                        $table = new \App\Db\Table\Publisher($this->adapter);
                        $table->deleteRecord($post['id']);
                    }
                }
            }
             //Merge publisher
            if ($post['action'] == 'merge_publisher') {
                if ($post['submitt'] == 'Find_Source') {
                    $table = new \App\Db\Table\Publisher($this->adapter);

                    return $table->findRecords($post['source_publisher']);
                }
            }
            //Cancel edit\delete
            if ($post['submitt'] == 'Cancel') {
                $table = new \App\Db\Table\Publisher($this->adapter);

                return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
            }
        }
        // default: blank/missing search
        $table = new \App\Db\Table\Publisher($this->adapter);

        return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $table = new \App\Db\Table\Publisher($this->adapter);
        $characs = $table->findInitialLetter();

        $query = $request->getqueryParams();
        $post = [];
        //$locs = [];
        if ($request->getMethod() == 'POST') {
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
                    'searchParams' => implode('&', $searchParams),
                    'adapter' => $this->adapter,
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
                    'searchParams' => implode('&', $searchParams),
                    'carat' => $characs,
                ]
            )
        );
        }
    }
}
