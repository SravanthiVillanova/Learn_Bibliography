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

    protected function doAdd($post)
    {
        if ($post['submitt'] == 'Save') {
            //echo "<pre>";print_r($post);echo"</pre>";
            $table = new \App\Db\Table\User($this->adapter);
            $table->insertRecords($post['newuser_name'], $post['new_username'], md5($post['new_user_pwd']), $post['access_level']);
        }
    }
    
    protected function doEdit($post)
    {
        if ($post['submitt'] == 'Save') {
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
    
    protected function doDelete($post)
    {
        if ($post['submitt'] == 'Delete') {
            if (!is_null($post['id'])) {
                //echo "delete";
                $table = new \App\Db\Table\User($this->adapter);
                $table->deleteRecord($post['id']);
            }
        }
    }
    
    protected function doAction($post)
    {
        //add new user
        if ($post['action'] == 'new') {
            $this->doAdd($post);
        }
        //edit user
        if ($post['action'] == 'edit') {
            $this->doEdit($post);
        }
        //delete user
        if ($post['action'] == 'delete') {
            $this->doDelete($post);
        }
		//change user module access
		if ($post['action'] == 'users_access') {
			$table = new \App\Db\Table\Module_Access($this->adapter);
			$all_modules = $table->getAllModules();
			foreach($all_modules as $row) :
				if (isset($post['access'][$row]))
				{
					if (isset($post['access'][$row]['Super User']))
					{
						$table = new \App\Db\Table\Module_Access($this->adapter);
						$table->setModuleAccess($row,'Super User');
					}
					elseif (!isset($post['access'][$row]['Super User']))
					{
						$table = new \App\Db\Table\Module_Access($this->adapter);
						$table->unsetModuleAccess($row,'Super User');
					}
					
					if (isset($post['access'][$row]['User']))
					{
						$table = new \App\Db\Table\Module_Access($this->adapter);
						$table->setModuleAccess($row,'User');
					}
					elseif (!isset($post['access'][$row]['User']))
					{
						$table = new \App\Db\Table\Module_Access($this->adapter);
						$table->unsetModuleAccess($row,'User');
					}
				}
				else
				{
					//for role superuser, set module to 0
					$table = new \App\Db\Table\Module_Access($this->adapter);
					$table->unsetModuleAccess($row,'Super User');
					
					//for role user,set module to 0
					$table = new \App\Db\Table\Module_Access($this->adapter);
					$table->unsetModuleAccess($row,'User');
				}
			endforeach;
		}
    }
    
    protected function getPaginator($post)
    {
        //add, edit, delete actions on user
       if (!empty($post['action'])) {
           //add edit delete users
            $this->doAction($post);
          
            //Cancel add\edit\delete
            if ($post['submitt'] == 'Cancel') {
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
