<?php
/**
 * Authentication Middleware
 *
 * PHP version 5
 *
 * Copyright (c) Falvey Library 2017.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https:// Main Page
 */
namespace VuBib\Middleware;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode\RFC\RFC7231;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;

/**
 * Class Definition for Authentication Middleware.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class AuthenticationMiddleware
{
    /**
     * Router\RouterInterface
     *
     * @var $router
     */
    private $router;

    /**
     * Template\TemplateRendererInterface
     *
     * @var $template
     */
    private $template;

    /**
     * String
     *
     * @var basePath
     */
    private $basePath;

    /**
     * Zend\Session\Container
     *
     * @var $session
     */
    private $session;

    /**
     * AuthenticationMiddleware constructor.
     *
     * @param Router\RouterInterface             $router   for routes
     * @param Template\TemplateRendererInterface $template for templates
     * @param string                             $basePath base url path
     * @param Zend\Session\Container             $session  zend session
     *
     * @return empty
     */
    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template,
        $basePath, $session
    ) {
        $this->router = $router;
        $this->template = $template;
        $this->basePath = rtrim($basePath, '/');
        $this->session = $session;
    }

    /**
     * Invokes required template
     *
     * @param ServerRequestInterface $request  server-side request.
     * @param ResponseInterface      $response response to client side.
     * @param callable               $next     CallBack Handler.
     *
     * @return RedirectResponse|HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        if (!isset($this->session->id)) {
            return new RedirectResponse(
                sprintf($this->basePath.'/login?redirect_to=%s', $this->getCurrentRequest($request)),
                RFC7231::FOUND
            );
        }

        return $next($request, $response);
    }

    /**
     * Get the current request uri
     *
     * @param ServerRequestInterface $request server-side request.
     *
     * @return string
     */
    private function getCurrentRequest(ServerRequestInterface $request)
    {
        /**
         * Uri
         *
         * @var UriInterface $uri
         */
        $uri = $request->getUri();

        $redirectTo = $this->basePath.$uri->getPath();

        if ($uri->getQuery() !== '') {
            $redirectTo .= '?'.$uri->getQuery();
        }

        if ($uri->getFragment() !== '') {
            $redirectTo .= '#'.$uri->getFragment();
        }

        return urlencode($redirectTo);
    }
}
