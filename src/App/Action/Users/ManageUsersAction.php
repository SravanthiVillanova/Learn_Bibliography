<?php

namespace App\Action\Users;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;

class ManageUsersAction
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
        //add, edit, delete actions on user
       if (!empty($post['action'])) {
           //add new user
            if ($post['action'] == 'new') {
                if ($post['submit_Save'] == 'Save') {
                    //echo "<pre>";print_r($post);echo"</pre>";
                    $table = new \App\Db\Table\User($this->adapter);
                    $table->insertRecords($post['newuser_name'], $post['new_username'], md5($post['new_user_pwd']), $post['access_level']);
                }
            }
            //edit a work type
            if ($post['action'] == 'edit') {
                if ($post['submit_Save'] == 'Save') {
                    if (!is_null($post['id'])) {
                        if (empty($post['edit_user_pwd'])) {
                            $pwd = null;
                        } else {
                            $pwd = md5($post['edit_user_pwd']);
                        }
                        $table = new \App\Db\Table\User($this->adapter);
                        $table->updateRecord($post['id'], $post['edituser_name'], $post['edit_username'], $pwd,
                                            $post['access_level']);
                    }
                }
            }
            //delete a work type
            if ($post['action'] == 'delete') {
                if ($post['submitt'] == 'Delete') {
                    if (!is_null($post['id'])) {
                        //echo "delete";
                        $table = new \App\Db\Table\User($this->adapter);
                        $table->deleteRecord($post['id']);
                    }
                }
            }
            //Cancel add\edit\delete
            if ($post['submit_Cancel'] == 'Cancel') {
                $table = new \App\Db\Table\User($this->adapter);

                return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
            }
       }
        // default: blank for listing in manage
        $table = new \App\Db\Table\User($this->adapter);

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

        return new HtmlResponse(
            $this->template->render(
                'app::users::manage_users',
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
