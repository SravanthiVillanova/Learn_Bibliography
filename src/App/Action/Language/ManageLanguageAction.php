<?php
/**
 * ISBN validation and conversion functionality
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
namespace App\Action\Language;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;

/**
 * Class Definition for ManageLanguageAction.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class ManageLanguageAction
{
    private $router;

    private $template;

    private $adapter;

    //private $dbh;
    //private $qstmt;

	/**
     * ManageLanguageAction constructor.
     *
     * @param Router\RouterInterface                  $router
     * @param Template\TemplateRendererInterface|null $template
     * @param Adapter             					  $adapter
     */
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

	/**
	* invokes required template
	**/
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
