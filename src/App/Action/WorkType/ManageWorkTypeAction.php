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
namespace App\Action\WorkType;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;

/**
 * Class Definition for ManageWorkTypeAction.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class ManageWorkTypeAction
{
    private $router;

    private $template;

    private $adapter;

	/**
     * ManageWorkTypeAction constructor.
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

    protected function doAdd($post)
    {
        if ($post['submitt'] == 'Save') {
            $table = new \App\Db\Table\WorkType($this->adapter);
            $table->insertRecords($post['new_worktype']);
        }
    }
    
    protected function doEdit($post)
    {
        if ($post['submitt'] == 'Save') {
            if (!is_null($post['id'])) {
                $table = new \App\Db\Table\WorkType($this->adapter);
                $table->updateRecord($post['id'], $post['edit_worktype']);
            }
        }
    }
    
    protected function doDelete($post)
    {
        if ($post['submitt'] == 'Delete') {
            if (!is_null($post['id'])) {
                $table = new \App\Db\Table\Work($this->adapter);
                $table->updateWorkTypeId($post['id']);
                $table = new \App\Db\Table\WorkType_WorkAttribute($this->adapter);
                $table->deleteRecordByWorkType($post['id']);
                $table = new \App\Db\Table\WorkType($this->adapter);
                $table->deleteRecord($post['id']);
            }
        }
    }
    
    protected function removeAttribute($post)
    {
        $attrs_to_remove = [];
        preg_match_all('/,?id_\d+/', $post['remove_attr'], $matches);
        foreach ($matches[0] as $id) :
                            $attrs_to_remove[] = (int) preg_replace("/^,?\w{2,3}_/", '', $id);
        endforeach;
        if (!is_null($attrs_to_remove)) {
            if (count($attrs_to_remove) != 0) {
                //remove attributes from a work type
                                $table = new \App\Db\Table\WorkType_WorkAttribute($this->adapter);
                $table->deleteAttributeFromWorkType($post['id'], $attrs_to_remove);
            }
        }
    }
    
    protected function addAttribute($post)
    {
        $attrs_to_add = [];
        preg_match_all('/,?nid_\d+/', $post['sort_order'], $matches);
        foreach ($matches[0] as $id) :
                            $attrs_to_add[] = (int) preg_replace("/^,?\w{2,3}_/", '', $id);
        endforeach;
        if (!is_null($attrs_to_add)) {
            if (count($attrs_to_add) != 0) {
                //Add attributes to work type
                $table = new \App\Db\Table\WorkType_WorkAttribute($this->adapter);
                $table->addAttributeToWorkType($post['id'], $attrs_to_add);
            }
        }
    }
    protected function doAttributeSort($post)
    {
        if ($post['submitt'] == 'Save') {
            if (!empty($post['remove_attr'])) {
                $this->removeAttribute($post);
            }
            if (!empty($post['sort_order'])) {
                $this->addAttribute($post);
            }
                    //after adding attrs to work type, adjust ranks
                    $table = new \App\Db\Table\WorkType_WorkAttribute($this->adapter);
            $table->updateWorkTypeAttributeRank($post['id'], $post['sort_order']);
        }
    }
    
    protected function doAction($post)
    {
        //add a new work type
        if ($post['action'] == 'new') {
            $this->doAdd($post);
        }
        //edit a work type
        if ($post['action'] == 'edit') {
            $this->doEdit($post);
        }
        //delete a work type
        if ($post['action'] == 'delete') {
            $this->doDelete($post);
        }
        //add, remove attributes to work type
        if ($post['action'] == 'sortable') {
            $this->doAttributeSort($post);
        }
    }
    
    protected function getPaginator($post)
    {
        //add, edit, delete actions on worktype
        if (!empty($post['action'])) {
            //add edit delete worktypes and manage attributes
            $this->doAction($post);
                        
            //Cancel add\edit\delete
            if ($post['submitt'] == 'Cancel') {
                $table = new \App\Db\Table\WorkType($this->adapter);

                return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
            }
        }
        // default: blank for listing in manage
        $table = new \App\Db\Table\WorkType($this->adapter);

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

        $searchParams = [];

        if (isset($post['action']) && $post['action'] == 'sortable' && $post['submitt'] == 'Save') {
            //if ($post['action'] == 'sortable' && $post['submitt'] == 'Save') {
                return new HtmlResponse(
            $this->template->render(
                'app::worktype::manage_worktypeattribute',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    'request' => $request,
                    'adapter' => $this->adapter,
                     'searchParams' => implode('&', $searchParams),
                ]
            )
        );
            //}
        } else {
            return new HtmlResponse(
            $this->template->render(
                'app::worktype::manage_worktype',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    'searchParams' => implode('&', $searchParams),
                ]
            )
        );
        }
    }
}
