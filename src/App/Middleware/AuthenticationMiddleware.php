<?php
namespace App\Middleware;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode\RFC\RFC7231;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;

class AuthenticationMiddleware
{
    /**
     * @var Router\RouterInterface
     */
    private $router;

    /**
     * @var null|Template\TemplateRendererInterface
     */
    private $template;

	/**
	 * @var string
	 */
	private $basePath;

	/**
	 * @var \Zend\Session\Container
	 */
	private $session;

    /**
     * AuthenticationMiddleware constructor
     *
     * @param Router\RouterInterface $router
     * @param Template\TemplateRendererInterface $template
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
     * Handle the authentication of a user
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return RedirectResponse|HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        if (!isset($this->session->id)) {
            return new RedirectResponse(
                sprintf($this->basePath . '/login?redirect_to=%s', $this->getCurrentRequest($request)),
                RFC7231::FOUND
            );
        }

        return $next($request, $response);
    }

    private function getCurrentRequest(ServerRequestInterface $request)
    {
        /** @var UriInterface $uri */
        $uri = $request->getUri();

        $redirectTo = $this->basePath . $uri->getPath();

        if ($uri->getQuery() !== '') {
            $redirectTo .= '?' . $uri->getQuery();
        }

        if ($uri->getFragment() !== '') {
            $redirectTo .= '#' . $uri->getFragment();
        }

        return urlencode($redirectTo);
    }
}