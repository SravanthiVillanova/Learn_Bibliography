<?php

namespace App\Action\WorkType;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;

class AttributesWorkTypeAction
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

    protected function getPaginator($post)
    {
        //add, edit, delete actions on attribute
        if (!empty($post['action'])) {
            //add new attribute
            if ($post['action'] == 'new') {
                if ($post['submitt'] == 'Save') {
                    /*echo '<pre>';
                    print_r($post);
                    echo '</pre>';*/
                    $table = new \App\Db\Table\WorkAttribute($this->adapter);
                    $table->addAttribute($post['new_attribute'], $post['field_type']);
                }
            }
            //edit attribute
            if ($post['action'] == 'edit') {
                if ($post['submitt'] == 'Save') {
                    if (!is_null($post['id'])) {
                        $table = new \App\Db\Table\WorkAttribute($this->adapter);
                        $table->updateRecord($post['id'], $post['edit_attribute']);
                    }
                }
            }
            //delete attribute
            if ($post['action'] == 'delete') {
                if ($post['submitt'] == 'Delete') {
                    if (!is_null($post['id'])) {
                        //no
                        $table = new \App\Db\Table\Work_WorkAttribute($this->adapter);
                        $table->deleteWorkAttributeFromWork($post['id']);

                        //yes
                        $table = new \App\Db\Table\WorkType_WorkAttribute($this->adapter);
                        $table->deleteAttributeFromAllWorkTypes($post['id']);

                        //yes
                        $table = new \App\Db\Table\WorkAttribute_Option($this->adapter);
                        $table->deleteWorkAttributeOptions($post['id']);

                        //no
                        $table = new \App\Db\Table\WorkAttribute($this->adapter);
                        $table->deleteRecord($post['id']);
                    }
                }
            }
            //Cancel add\edit\delete
            if ($post['submitt'] == 'Cancel') {
                $table = new \App\Db\Table\WorkAttribute($this->adapter);

                return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
            }
        }
        // default: blank for listing in manage
        $table = new \App\Db\Table\WorkAttribute($this->adapter);

        return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $query = $request->getqueryParams();
        $post = [];
        if ($request->getMethod() == 'POST') {
            $post = $request->getParsedBody();
        }
        $paginator = $this->getPaginator($post);
        $paginator->setDefaultItemCountPerPage(7);
        //$allItems = $paginator->getTotalItemCount();
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

        return new HtmlResponse(
            $this->template->render(
                'app::worktype::attributes_worktype',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    'searchParams' => implode('&', $searchParams),
                ]
            )
        );
    }
}
