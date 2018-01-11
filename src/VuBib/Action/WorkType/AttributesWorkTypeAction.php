<?php
/**
 * Attributes WorkType Action
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
use Zend\Paginator\Paginator;

/**
 * Class Definition for AttributesWorkTypeAction.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class AttributesWorkTypeAction
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

    //private $dbh;
    //private $qstmt;

    /**
     * AttributesWorkTypeAction constructor.
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
     * Adds worktype attributes.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    protected function doAdd($post)
    {
        if ($post['submitt'] == 'Save') {
            $table = new \VuBib\Db\Table\WorkAttribute($this->adapter);
            $table->addAttribute($post['new_attribute'], $post['field_type']);
        }
    }
    
    /**
     * Edits worktype attributes.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    protected function doEdit($post)
    {
        if ($post['submitt'] == 'Save') {
            if (!is_null($post['id'])) {
                $table = new \VuBib\Db\Table\WorkAttribute($this->adapter);
                $table->updateRecord($post['id'], $post['edit_attribute']);
            }
        }
    }
    
    /**
     * Deletes worktype attributes.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    protected function doDelete($post)
    {
        if ($post['submitt'] == 'Delete') {
            if (!is_null($post['id'])) {
                //no
                        $table = new \VuBib\Db\Table\Work_WorkAttribute($this->adapter);
                $table->deleteWorkAttributeFromWork($post['id']);

                        //yes
                        $table = new \VuBib\Db\Table\WorkType_WorkAttribute($this->adapter);
                $table->deleteAttributeFromAllWorkTypes($post['id']);

                        //yes
                        $table = new \VuBib\Db\Table\WorkAttribute_Option($this->adapter);
                $table->deleteWorkAttributeOptions($post['id']);

                        //no
                        $table = new \VuBib\Db\Table\WorkAttribute($this->adapter);
                $table->deleteRecord($post['id']);
            }
        }
    }
    
    /**
     * Action based on action parameter.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    protected function doAction($post)
    {
        //add new attribute
        if ($post['action'] == 'new') {
            $this->doAdd($post);
        }
        //edit attribute
        if ($post['action'] == 'edit') {
            $this->doEdit($post);
        }
        //delete attribute
        if ($post['action'] == 'delete') {
            $this->doDelete($post);
        }
    }
    
    /**
     * Call aprropriate function for each action.
     *
     * @param Array $post contains posted elements of form
     *
     * @return Paginator                  $paginator
     */
    protected function getPaginator($post)
    {
        //add, edit, delete actions on attribute
        if (!empty($post['action'])) {
            //add edit delete attribute
            $this->doAction($post);
            
            //Cancel add\edit\delete
            if ($post['submitt'] == 'Cancel') {
                $table = new \VuBib\Db\Table\WorkAttribute($this->adapter);

                return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
            }
        }
        // default: blank for listing in manage
        $table = new \VuBib\Db\Table\WorkAttribute($this->adapter);

        return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
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
        $simpleAction = new \VuBib\Action\SimpleRenderAction('vubib::worktype::attributes_worktype', $this->router, $this->template, $this->adapter);
        list($query, $post) = $simpleAction->getQueryAndPost($request);

        $paginator = $this->getPaginator($post);
        $paginator->setDefaultItemCountPerPage(7);
        //$allItems = $paginator->getTotalItemCount();

        $simpleAction = new \VuBib\Action\SimpleRenderAction('vubib::worktype::attributes_worktype', $this->router, $this->template, $this->adapter);
        $pgs = $simpleAction->getNextPrevious($paginator, $query);

        $searchParams = [];

        return new HtmlResponse(
            $this->template->render(
                'vubib::worktype::attributes_worktype',
                [
                    'rows' => $paginator,
                    'previous' => $pgs['prev'],
                    'next' => $pgs['nxt'],
                    'countp' => $pgs['cp'],
                    'searchParams' => implode('&', $searchParams),
                ]
            )
        );
    }
}
