<?php

namespace App\Action;

use App\Entity\AuthUserInterface;
use App\Repository\UserAuthenticationInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode\RFC\RFC7231;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

/**
 * Class LoginPageAction.
 */
class LoginPageAction
{
    /**
     * @var string
     */
    const PAGE_TEMPLATE = 'app::login-page';

    /**
     * @var Router\RouterInterface
     */
    private $router;

    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var UserAuthenticationInterface
     */
    private $userAuthenticationService;

    /**
     * @var Form
     */
    //private $form;

    /**
     * @var AuthUserInterface
     */
    //private $authEntity;

    /**
     * @var string
     */
    private $defaultRedirectUri;

    /**
     * @var \Zend\Session\Container
     */
    private $session;

    /**
     * LoginPageAction constructor.
     *
     * @param Router\RouterInterface                  $router
     * @param Template\TemplateRendererInterface|null $template
     * @param UserAuthenticationInterface             $userAuthenticationService
     * @param AuthUserInterface                       $authenticationEntity
     * @param string                                  $defaultRedirectUri
     */
    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template,
        UserAuthenticationInterface $userAuthenticationService,
        $defaultRedirectUri, Adapter $adapter, $session
    ) {
        $this->router = $router;
        $this->template = $template;
        $this->userAuthenticationService = $userAuthenticationService;
        $this->defaultRedirectUri = $defaultRedirectUri;
        $this->adapter = $adapter;
        $this->session = $session;
    }

    protected function doLogin($post)
    {
        $user1 = [];
        if ($post['action'] == 'login') {
            $table = new \App\Db\Table\User($this->adapter);
            $user = $table->checkUserAuthentication($post['user_name'], $post['user_pwd']);
            $user1 = array_reduce($user, 'array_merge', array());
        }
        return $user1;
    }
    
    protected function setModuleAccess($user1)
    {
        $this->session->id = $user1['id'];
        if (isset($user1['level'])) {
            if ($user1['level'] == 1) {
                $this->session->role = 'role_a';
            } else {
                $this->session->role = 'role_su';
            }
        } else {
            $this->session->role = 'role_u';
        }
        $table = new \App\Db\Table\Module_Access($this->adapter);
        $modules = $table->getModules($this->session->role);
        foreach ($modules as $row) :
                    $mods[] = $row['module'];
        endforeach;
        $this->session->modules_access = $mods;
    }
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        if ($request->getMethod() == 'POST') {
            $post = [];
            $post = $request->getParsedBody();
            if (!empty($post['action'])) {
                $user1 = $this->doLogin($post);
            }
            if (!(is_null($user1['id']))) {
                $this->setModuleAccess($user1);
                return new RedirectResponse(
                    $this->getRedirectUri($request),
                    RFC7231::FOUND
                );
            }
            return new RedirectResponse(
                $this->getRedirectUri($request),
                RFC7231::FOUND
            );
        }
        if (array_key_exists('logout', $request->getQueryParams())) {
            if ($request->getQueryParams()['logout'] == 'y') {
                $toUrl = $this->getRedirectUri($request);
                $sessionManager = $this->session->getManager();
                $sessionManager->destroy();

                return new RedirectResponse(
                    $toUrl,
                    RFC7231::FOUND
                );
            }
        }

        return $this->renderLoginFormResponse($request);
    }

    /**
     * Render an HTML reponse, containing the login form.
     *
     * Provide the functionality required to let a user authenticate, based on using an HTML form.
     *
     * @return HtmlResponse
     */
    private function renderLoginFormResponse($request)
    {
        return new HtmlResponse($this->template->render(self::PAGE_TEMPLATE, ['request' => $request,
                    'adapter' => $this->adapter, ]));
    }

    /**
     * Get the URL to redirect the user to.
     *
     * The value returned here is where to send the user to after a successful authentication has
     * taken place. The intent is to avoid the user being redirected to a generic route after
     * login, requiring them to have to specify where they want to navigate to.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function getRedirectUri(ServerRequestInterface $request)
    {
        if (array_key_exists('logout', $request->getQueryParams())) {
            $reqParams = $request->getServerParams();
            //$baseUrl = $uri->getScheme() . '://' . $uri->getHost() . '/' . $uri->getPath();
            $toUrl = 'http'.'://'.$reqParams['HTTP_HOST'].'/'.$reqParams['REDIRECT_URL'];
            return $toUrl.'?redirect_to=/VuBib/public/';
        }
        if (array_key_exists('redirect_to', $request->getQueryParams())) {
            return $request->getQueryParams()['redirect_to'];
        }
        //return $request->getQueryParams()['redirect_to'];
        return $this->defaultRedirectUri;
    }
}
