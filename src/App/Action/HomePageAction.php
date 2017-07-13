<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;

// Page Instructions Content
require_once 'instructions.inc';

global $instructions;

class HomePageAction
{
    private $router;

    private $template;

    //private $instructions;

    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null)
    {
        $this->router = $router;
        $this->template = $template;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $data = [];
        /*
        $this->layout()->instructions = "PRODUCTION - Live since 21st, November 2005

                                   //      Running on NEW server September 2007";
        $viewData = array(
        "layout" => "app::home-page.phtml",
        "title" => "Hello World Title",
        "instructions" => "PRODUCTION - Live since 21st, November 2005

                                         Running on NEW server September 2007"
                         );
        $html = $this->template->render("app::home-page.phtml", $viewData);
        return new HtmlResponse($html);
        $data = ['instructions' => 'foo']; */
        return new HtmlResponse($this->template->render('app::home-page', $this));
    }
}
