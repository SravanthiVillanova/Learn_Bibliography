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
 * Class Definition for ManageSubAttributesAction.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class ManageSubAttributesAction
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
            $table = new \VuBib\Db\Table\WorkAttribute_SubAttribute($this->adapter);
            $subattr_id = $table->addSubAttributeReturnId($post['wkat_id'], $post['new_subattr']);

            //get option ids of work attribute
            $table = new \VuBib\Db\Table\WorkAttribute_Option($this->adapter);
            $optIds = $table->getOptionIdsForAttribute($post['wkat_id']);

            //add a record for each option in attribute_option_subattribute table
            $table = new \VuBib\Db\Table\Attribute_Option_SubAttribute($this->adapter);
            foreach($optIds as $optId):
                $table->insertRecord($post['wkat_id'], $optId, $subattr_id);
            endforeach;
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
                $table = new \VuBib\Db\Table\WorkAttribute_SubAttribute($this->adapter);
                $table->editSubAttribute($post['id'], $post['wkat_id'], $post['edit_subattr']);
            }
        }
    }
    
    /**
     * Deletes sub attributes.
     *
     * @param Array $post  contains posted elements of form
     * @param Array $query url query parameters
     *
     * @return empty
     */
    protected function doDelete($post, $query)
    {
        if (isset($post['submitt'])) {
            if ($post['submitt'] == 'Delete') {
                if (!is_null($post['subattr_id'])) {
                    foreach($post['subattr_id'] as $subattr_Id):                        
                        $table = new \VuBib\Db\Table\Attribute_Option_SubAttribute($this->adapter);
                        $table->deleteRecordBySubAttributeId($subattr_Id);
                        
                        $table = new \VuBib\Db\Table\WorkAttribute_SubAttribute($this->adapter);
                        $table->deleteRecordById($subattr_Id);
                    endforeach;
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
    }
    
    /**
     * Searches attribute options.
     *
     * @param Array $post contains posted elements of form
     *
     * @return matched options
     */
    protected function searchOption($query)
    {
        if ($query['submit'] == 'Search') {
            if (!is_null($query['worktype_attr'])) {
                $table = new \VuBib\Db\Table\WorkAttribute_Option($this->adapter);
                return($table->findRecords($query['option'], $query['worktype_attr']));
            }
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
        $order = "";
        $wkat_id = isset($post['wkat_id']) ? $post['wkat_id'] : $query['wkat_id'];
        if (!empty($post['action'])) {
            //add edit delete sub attribute
            $this->doAction($post, $query);
            
            //Cancel add\edit\delete
            if ($post['submitt'] == 'Cancel') {
                $table = new \VuBib\Db\Table\WorkAttribute_SubAttribute($this->adapter);
                $paginator = $table->displaySubAttributes($wkat_id);

                return $paginator;
            }
        }
        
        // default: blank for listing in manage
        $table = new \VuBib\Db\Table\WorkAttribute_SubAttribute($this->adapter);
        $paginator = $table->displaySubAttributes($wkat_id, $order);

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
        $paginator->setDefaultItemCountPerPage(15);
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
        if (!empty($query['wkat_id'])) {
            $searchParams[] = 'wkat_id='.urlencode($query['wkat_id']);
        }
        $wkat_id = isset($post['wkat_id']) ? $post['wkat_id'] : $query['wkat_id'];
        /*if (isset($query['action']) && $query['action'] == 'search_option') {	
        $searchParams[] = 'action=search_option&worktype_attr='.urlencode($query['worktype_attr']).
			                  '&option='.urlencode($query['option']).'&submit=Search';
        }*/
        return new HtmlResponse(
            $this->template->render(
                'vubib::worktype::manage_subattributes',
                [
                'rows' => $paginator,
                    'wkat_id' => $wkat_id,
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
