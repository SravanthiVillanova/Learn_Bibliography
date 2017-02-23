<?php
namespace App\Action;

use App\Entity\AuthUserInterface;
use App\Repository\UserAuthenticationInterface;
use Zend\Session\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode\RFC\RFC7231;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;

/**
 * Class LoginPageAction
 * @package App\Action
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
    private $form;

    /**
     * @var AuthUserInterface
     */
    private $authEntity;

    /**
     * @var string
     */
    private $defaultRedirectUri;

    /**
     * LoginPageAction constructor.
     * @param Router\RouterInterface $router
     * @param Template\TemplateRendererInterface|null $template
     * @param UserAuthenticationInterface $userAuthenticationService
     * @param AuthUserInterface $authenticationEntity
     * @param string $defaultRedirectUri
     */
    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template,
        UserAuthenticationInterface $userAuthenticationService,
        AuthUserInterface $authenticationEntity,
        $defaultRedirectUri, Adapter $adapter
    ) {
        $this->router = $router;
        $this->template = $template;
        $this->authEntity = $authenticationEntity;
        $this->userAuthenticationService = $userAuthenticationService;
        $this->defaultRedirectUri = $defaultRedirectUri;
		$this->adapter  = $adapter;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return HtmlResponse
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
		var_dump("entered invoke");
        $session = new \Zend\Session\Container('Bibliography');

        if ($request->getMethod() == 'POST') {
			echo "entered get method post";
            //$user = false; // TODO -- get user
			$post = [];
            try {
				var_dump("entered try");
				$post = $request->getParsedBody();
				var_dump($post);
				//$login_status = "";
				if(!empty($post['action'])){
					var_dump("entered action not empty");
					if($post['action'] == 'login') {
						var_dump("entered action is login");
						$table = new \App\Db\Table\User($this->adapter);
						$user = $table->checkUserAuthentication($post['user_name'], $post['user_pwd']);
						var_dump($user);
						die();
					//$login_status = "logged in";
					}
				}
                $session->id = $this->userAuthenticationService->authenticateUser(
                    $post['user_name'],
                    $post['user_pwd']
                );
                return new RedirectResponse(
                    $this->getRedirectUri($request),
                    RFC7231::FOUND
                );
            } catch (UserAuthenticationException $e) {
				var_dump("entered catch");
                return $this->renderLoginFormResponse($request);
            }
        }

        return $this->renderLoginFormResponse($request);
    }


    /**
     * Render an HTML reponse, containing the login form
     *
     * Provide the functionality required to let a user authenticate, based on using an HTML form.
     *
     * @return HtmlResponse
     */
    private function renderLoginFormResponse($request)
    {
			//var_dump("entered renderResponse-else");
		return new HtmlResponse($this->template->render(self::PAGE_TEMPLATE, ['request' => $request,
                    'adapter' => $this->adapter,]));

		/*return new HtmlResponse(
            $this->template->render(
                'layout/default',
                [
                    //'rows' => $paginator,
                    //'previous' => $previous,
                    //'next' => $next,
                   // 'countp' => $countPages,
				   'request' => $request,
                    'adapter' => $this->adapter,
                ]
            )
        );*/
    }

    /**
     * Get the URL to redirect the user to
     *
     * The value returned here is where to send the user to after a successful authentication has
     * taken place. The intent is to avoid the user being redirected to a generic route after
     * login, requiring them to have to specify where they want to navigate to.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    private function getRedirectUri(ServerRequestInterface $request)
    {
		var_dump("entered getRedirectUri"); //die();
        if (array_key_exists('redirect_to', $request->getQueryParams())) {
			var_dump("entered getRedirectUri-If"); //die();
            return $request->getQueryParams()['redirect_to'];
        }
		//return $request->getQueryParams()['redirect_to'];
        return $this->defaultRedirectUri;
    }
}