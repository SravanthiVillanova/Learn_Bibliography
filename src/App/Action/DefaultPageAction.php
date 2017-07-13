<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;

class DefaultPageAction
{
    private $router;

    private $template;

    private $adapter;

    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, Adapter $adapter)
    {
        $this->router = $router;
        $this->template = $template;
        $this->adapter = $adapter;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        /* $query = $request->getqueryParams();
        $post = [];
        if ($request->getMethod() == "POST") {
                $post = $request->getParsedBody();
        if($query['action'] == 'new'){
        if ($post['submitt'] == "Save") {
                        $table = new \App\Db\Table\UserTest($this->adapter);
                        $table->insertRecords($post['user_name'], $post['user_pwd'], $post['role']);
                    }
        //return new HtmlResponse($this->template->render('app::default', $this));
        }
       } */
       //var_dump("HELLO");
        return new HtmlResponse($this->template->render('app::default', ['request' => $request, 'adapter' => $this->adapter]));
    }
}
