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
namespace App\Action\Agent;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;

/**
 * Class Definition for ManageAgentAction.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class ManageAgentAction
{
    private $router;

    private $template;

    private $adapter;

	/**
     * ManageAgentAction constructor.
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

    protected function searchAgent($params)
    {
        // search by letter
        if (!empty($params['letter'])) {
            $table = new \App\Db\Table\Agent($this->adapter);

            return $table->displayRecordsByName($params['letter']);
        }
        // search by first name
        if (!empty($params['find_agentfname'])) {
            $table = new \App\Db\Table\Agent($this->adapter);

            return $table->findRecords($params['find_agentfname'], 'fname');
        }
        // search by last name
        if (!empty($params['find_agentlname'])) {
            $table = new \App\Db\Table\Agent($this->adapter);

            return $table->findRecords($params['find_agentlname'], 'lname');
        }
        // search by alternate name
        if (!empty($params['find_agentaltname'])) {
            $table = new \App\Db\Table\Agent($this->adapter);

            return $table->findRecords($params['find_agentaltname'], 'altname');
        }
        // search by organization name
        if (!empty($params['find_agentorgname'])) {
            $table = new \App\Db\Table\Agent($this->adapter);

            return $table->findRecords($params['find_agentorgname'], 'orgname');
        }
    }
    
    protected function doMerge($post)
    {
        // Switch Agent
        $table = new \App\Db\Table\WorkAgent($this->adapter);
        $table->updateRecordByAgentId($post['mrg_src_id'], $post['mrg_dest_id']);
        // Purge
        $table = new \App\Db\Table\Agent($this->adapter);
        $table->deleteRecord($post['mrg_dest_id']);
    }
    
    protected function doAction($post)
    {
        //add a new agent
            if ($post['action'] == 'new') {
                if ($post['submitt'] == 'Save') {
                    $table = new \App\Db\Table\Agent($this->adapter);
                    $table->insertRecords($post['new_agentfirstname'], $post['new_agentlastname'],
                                              $post['new_agentaltname'], $post['new_agentorgname']);
                }
            }
            //edit an agent
            if ($post['action'] == 'edit') {
                if ($post['submitt'] == 'Save') {
                    if (!is_null($post['id'])) {
                        $table = new \App\Db\Table\Agent($this->adapter);
                        $table->updateRecord($post['id'], $post['edit_agentfirstname'], $post['edit_agentlastname'],
                                            $post['edit_agentaltname'], $post['edit_agentorgname']);
                    }
                }
            }
            //delete an agent
            if ($post['action'] == 'delete') {
                if ($post['submitt'] == 'Delete') {
                    $this->doDelete($post);
                }
            }
            //merge agents
            if ($post['action'] == 'merge') {
                $this->doMerge($post);
            }
    }
    
    protected function doDelete($post)
    {
        if (!is_null($post['id'])) {
            $table = new \App\Db\Table\WorkAgent($this->adapter);
            $table->deleteRecordByAgentId($post['id']);
            $table = new \App\Db\Table\Agent($this->adapter);
            $table->deleteRecord($post['id']);
        }
    }
    protected function getPaginator($params, $post)
    {
        //search
        if (!empty($params)) {
            if (!empty($params['letter']) || !empty($params['find_agentfname']) || !empty($params['find_agentlname'])
                || !empty($params['find_agentaltname']) || !empty($params['find_agentorgname'])) {
                return ($this->searchAgent($params));
            }
        }
       
        //edit, delete actions on agent
        if (!empty($post['action'])) {
            //add edit delete merge agent
            $this->doAction($post);
            
            //Cancel edit\delete
            if ($post['submitt'] == 'Cancel') {
                $table = new \App\Db\Table\Agent($this->adapter);

                return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
            }
        }
        // default: blank for listing in manage
        $table = new \App\Db\Table\Agent($this->adapter);

        return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
    }

    protected function getSearchParams($query)
    {
        $searchParams = [];
        if (!empty($query['find_agentfname'])) {
            $searchParams[] = 'find_agentfname='.urlencode($query['find_agentfname']);
        }
        if (!empty($query['find_agentlname'])) {
            $searchParams[] = 'find_agentlname='.urlencode($query['find_agentlname']);
        }
        if (!empty($query['find_agentaltname'])) {
            $searchParams[] = 'find_agentaltname='.urlencode($query['find_agentaltname']);
        }
        if (!empty($query['find_agentorgname'])) {
            $searchParams[] = 'find_agentorgname='.urlencode($query['find_agentorgname']);
        }
        if (!empty($query['letter'])) {
            $searchParams[] = 'letter='.urlencode($query['letter']);
        }
        return $searchParams;
    }
    
	/**
	* invokes required template
	**/
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $table = new \App\Db\Table\Agent($this->adapter);
        $characs = $table->findInitialLetter();

        $query = $request->getqueryParams();
        $post = [];
        if ($request->getMethod() == 'POST') {
            $post = $request->getParsedBody();
        }
        $paginator = $this->getPaginator($query, $post);
        $paginator->setDefaultItemCountPerPage(7);
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

        $searchParams = $this->getSearchParams($query);
        
        if (!is_null($searchParams)) {
            $searchParams = implode('&', $searchParams);
        } else {
            $searchParams = '';
        }
        
        return new HtmlResponse(
            $this->template->render(
                'app::agent::manage_agent',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    'searchParams' => $searchParams,
                    'carat' => $characs,
                ]
            )
        );
    }
}
