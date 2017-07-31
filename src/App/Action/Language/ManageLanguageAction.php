<?php

namespace App\Action\Language;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;

class ManageLanguageAction
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

    protected function doAction($post)
    {
        //add a new language term
            if ($post['action'] == 'new') {
                if ($post['submitt'] == 'Save') {
                    $table = new \App\Db\Table\TranslateLanguage($this->adapter);
                    $table->insertRecords($_POST['de_newlang'], $_POST['en_newlang'], $_POST['es_newlang'], $_POST['fr_newlang'],
                                              $_POST['it_newlang'], $_POST['nl_newlang']
                                              );
                }
            }
            //edit a language term
            if ($post['action'] == 'edit') {
                if ($post['submitt'] == 'Save') {
                    if (!is_null($post['id'])) {
                        $table = new \App\Db\Table\TranslateLanguage($this->adapter);
                        $table->updateRecord($_POST['id'], $_POST['de_newlang'], $_POST['en_newlang'], $_POST['es_newlang'],
                                                    $_POST['fr_newlang'], $_POST['it_newlang'], $_POST['nl_newlang']
                                                );
                    }
                }
            }
            //delete a language term
            if ($post['action'] == 'delete') {
                if ($post['submitt'] == 'Delete') {
                    if (!is_null($post['id'])) {
                        $table = new \App\Db\Table\TranslateLanguage($this->adapter);
                        $table->deleteRecord($post['id']);
                    }
                }
            }
    }
    
    protected function getPaginator($post)
    {
        //edit, delete actions on language
        if (!empty($post['action'])) {
            //add edit delete language
            $this->doAction($post);
            
            //Cancel edit\delete
            if ($post['submitt'] == 'Cancel') {
                $table = new \App\Db\Table\TranslateLanguage($this->adapter);

                return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
            }
        }
        // default: blank for listing in manage
        $table = new \App\Db\Table\TranslateLanguage($this->adapter);

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

       /* $searchParams = [];
        if (!empty($query['name'])) {
            $searchParams[] = 'name=' . urlencode($query['name']);
        }
        if (!empty($query['location'])) {
            $searchParams[] = 'location=' . urlencode($query['location']);
        } */

        return new HtmlResponse(
            $this->template->render(
                'app::language::manage_language',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    //'searchParams' => implode('&', $searchParams),
                ]
            )
        );
    }
}
