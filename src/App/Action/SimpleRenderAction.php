<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;

class SimpleRenderAction
{
    private $router;
    private $template;
    private $templateName;
    private $adapter;

    public function __construct($templateName, Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, Adapter $adapter)
    {
        $this->templateName = $templateName;
        $this->router = $router;
        $this->template = $template;
        $this->adapter = $adapter;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        return new HtmlResponse($this->template->render($this->templateName, ['request' => $request, 'adapter' => $this->adapter]));
    }
}
