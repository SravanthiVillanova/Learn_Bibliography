<?php

namespace App\Action\Preferences;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;

class ChangePasswordPreferencesAction
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
        $post = [];
        if ($request->getMethod() == 'POST') {
            $post = $request->getParsedBody();
        }
        
        if (!empty($post['action'])) {
            //change user password
            if ($post['action'] == 'change_pwd') {
                if (!is_null($post['user'])) {
                    if ($post['submit_Save'] == 'Save') {
                        $table = new \App\Db\Table\User($this->adapter);
                        $table->changePassword($post['user'], $post['change_pwd']);
                    }
                }
            }
        }
       
        return new HtmlResponse(
        $this->template->render(
            'app::preferences::changepassword_preferences',
            [
                'request' => $request,
                'adapter' => $this->adapter
            ]
        )
      );
    }
}
