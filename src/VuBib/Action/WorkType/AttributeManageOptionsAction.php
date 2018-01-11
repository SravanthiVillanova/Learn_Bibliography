<?php
/**
 * Attribute Manage Options Action
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
namespace VuBib\Action\WorkType;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;

/**
 * Class Definition for AttributeManageOptionsAction.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class AttributeManageOptionsAction
{
    /**
     * Router\RouterInterface
     *
     * @var $router
     */
    protected $router;
    
    /**
     * Template\TemplateRendererInterface
     *
     * @var $template
     */
    protected $template;
    
    /**
     * Zend\Db\Adapter\Adapter
     *
     * @var $adapter
     */
    protected $adapter;

    /**
     * AttributeManageOptionsAction constructor.
     *
     * @param Router\RouterInterface             $router   for routes
     * @param Template\TemplateRendererInterface $template for templates
     * @param Adapter                            $adapter  for db connection
     */
    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, Adapter $adapter)
    {
        $this->router = $router;
        $this->template = $template;
        $this->adapter = $adapter;
    }
    
    /**
     * Adds attribute options.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    protected function doAdd($post)
    {
        if ($post['submitt'] == 'Save') {
            $table = new \VuBib\Db\Table\WorkAttribute_Option($this->adapter);
            $table->addOption($post['id'], $post['new_option'], $post['option_value']);
        }
    }
    
    /**
     * Edits attribute options.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    protected function doEdit($post)
    {
        if ($post['submitt'] == 'Save') {
            if (!is_null($post['id'])) {
                $table = new \VuBib\Db\Table\WorkAttribute_Option($this->adapter);
                $table->updateOption($post['id'], $post['edit_option'], $post['edit_value']);
            }
        }
    }
    
    /**
     * Deletes attribute options.
     *
     * @param Array $post  contains posted elements of form
     * @param Array $query url query parameters
     *
     * @return empty
     */
    protected function doDelete($post, $query)
    {
        if ($post['submitt'] == 'Delete') {
            if (!is_null($post['id'])) {
                $table = new \VuBib\Db\Table\Work_WorkAttribute($this->adapter);
                $table->deleteRecordByValue($query['id'], $post['id']);
                $table = new \VuBib\Db\Table\WorkAttribute_Option($this->adapter);
                $table->deleteOption($query['id'], $post['id']);
            }
        }
    }
    
    /**
     * Merges attribute options.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    protected function doMerge($post)
    {
        if ($post['submitt'] == 'Merge') {
            if (!is_null($post['workattribute_id'])) {
                for ($i = 0; $i < count($post['option_title']); ++$i) {
                    $table = new \VuBib\Db\Table\WorkAttribute_Option($this->adapter);
                    $rows = $table->getDuplicateOptionRecords($post['workattribute_id'], $post['option_title'][$i], $post['option_id'][$i]);

                    for ($j = 0; $j < count($rows); ++$j) {
                        $table = new \VuBib\Db\Table\Work_WorkAttribute($this->adapter);
                        $table->updateWork_WorkAttributeValue($post['workattribute_id'], $post['option_id'][$i], $rows[$j]['id']);

                        $table = new \VuBib\Db\Table\WorkAttribute_Option($this->adapter);
                        $table->deleteOption($post['workattribute_id'], $rows[$j]['id']);
                    }
                }
            }
        }
    }
    
    /**
     * Action based on action parameter.
     *
     * @param Array $post  contains posted elements of form
     * @param Array $query url query parameters
     *
     * @return empty
     */
    protected function doAction($post, $query)
    {
        //add new option
        if ($post['action'] == 'new') {
            $this->doAdd($post);
        }
        //edit option
        if ($post['action'] == 'edit') {
            $this->doEdit($post);
        }
        //delete option
        if ($post['action'] == 'delete') {
            $this->doDelete($post, $query);
        }
        //Merge option
        if ($post['action'] == 'merge') {
            $this->doMerge($post);
        }
    }
    
    /**
     * Call aprropriate function for each action.
     *
     * @param Array $query url query parameters
     * @param Array $post  contains posted elements of form
     *
     * @return Paginator                  $paginator
     */
    protected function getPaginator($query, $post)
    {
        if (!empty($post['action'])) {
            //add edit delete merge option
            $this->doAction($post, $query);
            
            //Cancel add\edit\delete
            if ($post['submitt'] == 'Cancel') {
                $table = new \VuBib\Db\Table\WorkAttribute_Option($this->adapter);
                $paginator = $table->displayAttributeOptions($query['id']);

                return $paginator;
            }
        }
        // default: blank for listing in manage
        $table = new \VuBib\Db\Table\WorkAttribute_Option($this->adapter);
        $paginator = $table->displayAttributeOptions($query['id']);

        return $paginator;
    }

    /**
     * Invokes required template
     *
     * @param ServerRequestInterface $request  server-side request.
     * @param ResponseInterface      $response response to client side.
     * @param callable               $next     CallBack Handler.
     *
     * @return HtmlResponse
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $countPages = 0;
        $query = $request->getqueryParams();
        /*if (!empty($query['action'])) {
            $action = $query['action'];
        }*/
        $post = [];
        if ($request->getMethod() == 'POST') {
            $post = $request->getParsedBody();
        }
        $paginator = $this->getPaginator($query, $post);
        $paginator->setDefaultItemCountPerPage(10);
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

        $searchParams = [];
        if (!empty($query['id'])) {
            $searchParams[] = 'id='.urlencode($query['id']);
        }

        return new HtmlResponse(
            $this->template->render(
                'vubib::worktype::manage_attribute_options',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    'searchParams' => implode('&', $searchParams),
                    'request' => $request,
                    'adapter' => $this->adapter,
                ]
            )
        );
    }
}
