<?php
/**
 * Manage Work Action
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
namespace App\Action\Work;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;

/**
 * Class Definition for ManageWorkAction.
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link https://
 */
class ManageWorkAction
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
     * ManageWorkAction constructor.
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
     * Fetches distinct initial letters of each work.
     *
     * @param Array $params url query parameters
     *
     * @return Array
     */
    protected function workLetters($params)
    {
        if (isset($params['action'])) {
            //review records
            if ($params['action'] == 'review') {
                $table = new \App\Db\Table\Work($this->adapter);

                return $table->displayReviewRecordsByLetter($params['letter']);
            } elseif ($params['action'] == 'classify') {
                //classify records
                $table = new \App\Db\Table\Work($this->adapter);

                return $table->displayClassifyRecordsByLetter($params['letter']);
            }
        } else {
            $table = new \App\Db\Table\Work($this->adapter);

            return $table->displayRecordsByName($params['letter']);
        }
    }

    /**
     * Work > Search.
     *
     * @param Array $params url query parameters
     *
     * @return paginator $paginator
     */
    protected function findWork($params)
    {
        $table = new \App\Db\Table\Work($this->adapter);
        $paginator = $table->findRecords($params['find_worktitle']);

        return $paginator;
    }
    
    /**
     * Fetch works to be reviewed and classified.
     *
     * @param Array $params url query parameters
     *
     * @return paginator $paginator
     */
    protected function workReviewClassify($params)
    {
        //Display works which need review
        if ($params['action'] == 'review') {
            $table = new \App\Db\Table\Work($this->adapter);
            $paginator = $table->fetchReviewRecords();

            return $paginator;
        }
        //Display works which are to be classified under folders
        if ($params['action'] == 'classify') {
            $table = new \App\Db\Table\Work($this->adapter);
            $paginator = $table->fetchClassifyRecords();

            return $paginator;
        }
    }

    /**
     * Adds new work.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    protected function doAdd($post)
    {
        if (isset($post['submit_save'])) {
            if ($post['submit_save'] == 'Save') {
                //insert General(work)
                $table = new \App\Db\Table\Work($this->adapter);
                $wk_id = $table->insertRecords(
                    $post['work_type'], $post['new_worktitle'], $post['new_worksubtitle'],
                    $post['new_workparalleltitle'], $post['description'], date('Y-m-d H:i:s'),
                    $post['user'], $post['select_workstatus'], $post['pub_yrFrom']
                );
                                    
                //extract classification rows
                foreach ($post['arr'] as $row):
                    $fl[] = explode(',', trim($row, ','));
                endforeach;

                //extract folder ids for each row
                for ($i = 0; $i < count($fl); ++$i) {
                        $folder[$i] = $fl[$i][count($fl[$i]) - 1];
                }

                //insert classification(work_folder)
                if ($folder[0] != null) {
                        $table = new \App\Db\Table\Work_Folder($this->adapter);
                        $table->insertWorkFolderRecords($wk_id, $folder);
                }
                    
                //insert Publisher(work_publisher)
                if ($post['pub_id'][0] != null) {
                        $table = new \App\Db\Table\WorkPublisher($this->adapter);
                        $table->insertRecords($wk_id, $post['pub_id'], $post['pub_location'], $post['pub_yrFrom'], $post['pub_yrTo']);
                        //$table->insertRecords($wk_id,$post['pub_id'],$post['publoc_id'],$post['pub_yrFrom'],$post['pub_yrTo']);
                }
                    
                //insert Agent(work_agent)
                if ($post['agent_id'][0] != null) {
                        $table = new \App\Db\Table\WorkAgent($this->adapter);
                        $table->insertRecords($wk_id, $post['agent_id'], $post['agent_type']);
                }
                    
                //map work to citation(work_workattribute)
                $wkat_id = [];
                foreach ($post as $key => $value) {
                    if ((preg_match("/^[a-z]+\,\d+[a-z]+\,\d+$/", $key)) && ($value != null)) {
                        $keys = preg_split("/[a-z]+\,/", $key);
                        $wkat_id[] = $keys[1];
                        $wkopt_id[] = $keys[2];
                    }
                    if ((preg_match("/^[a-z]+\,\d+$/", $key)) && ($value != null)) {
                        $wkat_id[] = preg_replace("/^[a-z]+\,/", '', $key).'<br />';
                        $wkopt_id[] = $value;
                    }
                }
                if (count($wkat_id) > 0) {
                    $table = new \App\Db\Table\Work_WorkAttribute($this->adapter);
                    $table->insertRecords($wk_id, $wkat_id, $wkopt_id);
                }
            }
        }
    }

    /**
     * Edits work.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    protected function doEdit($post)
    {
        if (isset($post['submit_save'])) {
            if ($post['submit_save'] == 'Save') {
                //update General(work)
                $table = new \App\Db\Table\Work($this->adapter);
                $table->updateRecords(
                    $post['id'], $post['edit_work_type'], $post['edit_worktitle'], $post['edit_worksubtitle'],
                    $post['edit_workparalleltitle'], $post['description'], date('Y-m-d H:i:s'),
                    $post['user'], $post['edit_workstatus'], $post['pub_yrFrom']
                );
                    
                //extract classification rows
                if (isset($post['arr'])) {
                    foreach ($post['arr'] as $row):
                        $fl[] = explode(',', trim($row, ','));
                    endforeach;
                    //extract folder ids for each row
                    for ($i = 0; $i < count($fl); ++$i) {
                        $folder[$i] = $fl[$i][count($fl[$i]) - 1];
                    }
                    
                    //update classification(work_folder)
                    if ($folder[0] != null) {
                        //delete all workfolders
                        $table = new \App\Db\Table\Work_Folder($this->adapter);
                        $table->deleteRecordByWorkId($post['id']);

                        //insert all workfolders again
                        $table = new \App\Db\Table\Work_Folder($this->adapter);
                        $table->insertWorkFolderRecords($post['id'], $folder);
                    }
                } else {
                        //delete all workfolders
                        $table = new \App\Db\Table\Work_Folder($this->adapter);
                        $table->deleteRecordByWorkId($post['id']);
                }
                    
                //update Publisher(work_publisher)
                if (isset($post['pub_id'])) {
                    if ($post['pub_id'][0] != null) {
                        //delete all publishers
                        $table = new \App\Db\Table\WorkPublisher($this->adapter);
                        $table->deleteRecordByWorkId($post['id']);

                        //insert all publishers again
                        $table = new \App\Db\Table\WorkPublisher($this->adapter);
                        $table->insertRecords($post['id'], $post['pub_id'], $post['pub_location'], $post['pub_yrFrom'], $post['pub_yrTo']);
                        //$table->insertRecords($post['id'], $post['pub_id'], $post['publoc_id'], $post['pub_yrFrom'], $post['pub_yrTo']);
                    }
                } else {
                    //delete all publishers
                    $table = new \App\Db\Table\WorkPublisher($this->adapter);
                    $table->deleteRecordByWorkId($post['id']);
                }
                    
                //update Agent(work_agent)
                if (isset($post['agent_id'])) {
                    if ($post['agent_id'][0] != null) {
                        //delete all agents
                        $table = new \App\Db\Table\WorkAgent($this->adapter);
                        $table->deleteRecordByWorkId($post['id']);

                        //insert Agents again
                        $table = new \App\Db\Table\WorkAgent($this->adapter);
                        $table->insertRecords($post['id'], $post['agent_id'], $post['agent_type']);
                    }
                } else {
                    //delete all agents
                    $table = new \App\Db\Table\WorkAgent($this->adapter);
                    $table->deleteRecordByWorkId($post['id']);
                }
                    
                //map work to citation(work_workattribute)
                $wkat_id = [];
                foreach ($post as $key => $value) {
                    if ((preg_match("/^[a-z]+\,\d+[a-z]+\,\d+$/", $key)) && ($value != null)) {
                        $keys = preg_split("/[a-z]+\,/", $key);
                        $wkat_id[] = $keys[1];
                        $wkopt_id[] = $keys[2];
                    }
                    if ((preg_match("/^[a-z]+\,\d+$/", $key)) && ($value != null)) {
                        $wkat_id[] = preg_replace("/^[a-z]+\,/", '', $key).'<br />';
                        $wkopt_id[] = $value;
                    }
                }
                if ($wkat_id[0] != null) {
                    //delete workattribute records
                    $table = new \App\Db\Table\Work_WorkAttribute($this->adapter);
                    $table->deleteRecordByWorkId($post['id']);

                    //insert workattributes again
                    $table = new \App\Db\Table\Work_WorkAttribute($this->adapter);
                    $table->insertRecords($post['id'], $wkat_id, $wkopt_id);
                } else {
                    //delete workattribute records
                    $table = new \App\Db\Table\Work_WorkAttribute($this->adapter);
                    $table->deleteRecordByWorkId($post['id']);
                }
            }
        }
    }
    
    /**
     * Deletes work.
     *
     * @param Array $post contains posted elements of form
     *
     * @return empty
     */
    protected function doDelete($post)
    {
        if (isset($post['submitt'])) {
            if ($post['submitt'] == 'Delete') {
                if (!is_null($post['work_id'])) {
                    $table = new \App\Db\Table\WorkAgent($this->adapter);
                    $table->deleteRecordByWorkId($post['work_id']);
                    $table = new \App\Db\Table\Work_Folder($this->adapter);
                    $table->deleteRecordByWorkId($post['work_id']);
                    $table = new \App\Db\Table\WorkPublisher($this->adapter);
                    $table->deleteRecordByWorkId($post['work_id']);
                    $table = new \App\Db\Table\Work_WorkAttribute($this->adapter);
                    $table->deleteRecordByWorkId($post['work_id']);
                    $table = new \App\Db\Table\Work($this->adapter);
                    $table->deleteRecordByWorkId($post['work_id']);
                }
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
        //add a new work type
        if ($post['action'] == 'work_new') {
            $this->doAdd($post);
        }
        if ($post['action'] == 'work_edit') {
            $this->doEdit($post);
        }
        if ($post['action'] == 'delete') {
            $this->doDelete($post);
        }
    }

    /**
     * Get records to display.
     *
     * @param Array $params url query parameters
     * @param Array $post   contains posted elements of form
     *
     * @return Paginator                  $paginator
     */  
    protected function getPaginator($params, $post)
    {
        // search by letter
        if (!empty($params['letter'])) {
            return ($this->workLetters($params));
        }
        // Work Lookup
        if (!empty($params['find_worktitle'])) {
            return($this->findWork($params));
            //echo "name is " . $params['find_worktitle'];
        }
        if (!empty($params['action'])) {
            return($this->workReviewClassify($params));
        }
        if (!empty($post['action'])) {
            //add edit delete work
            $this->doAction($post);
        }

        //Cancel edit\delete
        if (isset($post['submit_cancel'])) {
            if ($post['submit_cancel'] == 'Cancel') {
                $table = new \App\Db\Table\Work($this->adapter);

                return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
            }
        }
        // default: blank/missing search
        $table = new \App\Db\Table\Work($this->adapter);

        return new Paginator(new \Zend\Paginator\Adapter\DbTableGateway($table));
    }

    /**
     * Set search parameters for pagination.
     *
     * @param Array $query url query parameters
     *
     * @return Paginator                  $paginator
     */
    protected function getSearchParams($query)
    {
        $searchParams = [];
        if (!empty($query['find_worktitle'])) {
            $searchParams[] = 'find_worktitle='.urlencode($query['find_worktitle']);
        }
        if (!empty($query['letter']) && $query['action'] == 'alphasearch') {
            $searchParams[] = 'letter='.urlencode($query['letter']);
        }
        if (isset($query['action'])) {
            if ($query['action'] == 'review') {
                if (!empty($query['letter'])) {
                    $searchParams[] = 'action='.urlencode($query['action']).'&letter='.urlencode($query['letter']);
                } else {
                    $searchParams[] = 'action='.urlencode($query['action']);
                }
            }
            if ($query['action'] == 'classify') {
                if (!empty($query['letter'])) {
                    $searchParams[] = 'action='.urlencode($query['action']).'&letter='.urlencode($query['letter']);
                } else {
                    $searchParams[] = 'action='.urlencode($query['action']);
                }
            }
        }
        return $searchParams;
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
        $simpleAction = new \App\Action\SimpleRenderAction('app::agent::manage_agent', $this->router, $this->template, $this->adapter);
        list($query,$post) = $simpleAction->getQueryAndPost($request);

        if (isset($query['action'])) {
            if ($query['action'] == 'review') {
                $table = new \App\Db\Table\Work($this->adapter);
                $characs = $table->findInitialLetterReview();
            } elseif ($query['action'] == 'classify') {
                $table = new \App\Db\Table\Work($this->adapter);
                $characs = $table->findInitialLetterClassify();
            }
        } else {
            $table = new \App\Db\Table\Work($this->adapter);
            $characs = $table->findInitialLetter();
        }

        $paginator = $this->getPaginator($query, $post);
        $paginator->setDefaultItemCountPerPage(20);

        $simpleAction = new \App\Action\SimpleRenderAction('app::agent::manage_agent', $this->router, $this->template, $this->adapter);
        $pgs = $simpleAction->getNextPrevious($paginator, $query);
        
        $searchParams = $this->getSearchParams($query);
        
        if (!is_null($searchParams)) {
            $searchParams = implode('&', $searchParams);
        } else {
            $searchParams = '';
        }

        if (isset($query['action'])) {
            if ($query['action'] == 'review') {
                //echo "entered if";
                return new HtmlResponse(
                    $this->template->render(
                        'app::work::review_work',
                        [
                        'rows' => $paginator,
                        'previous' => $pgs['prev'],
                        'next' => $pgs['nxt'],
                        'countp' => $pgs['cp'],
                        'searchParams' => $searchParams,
                        'carat' => $characs,
                        'request' => $request,
                        'adapter' => $this->adapter,
                        ]
                    )
                );
            }
            if ($query['action'] == 'classify') {
                //echo "entered else if";
                return new HtmlResponse(
                    $this->template->render(
                        'app::work::classify_work',
                        [
                        'rows' => $paginator,
                        'previous' => $pgs['prev'],
                        'next' => $pgs['nxt'],
                        'countp' => $pgs['cp'],
                        'searchParams' => $searchParams,
                        'carat' => $characs,
                        'request' => $request,
                        'adapter' => $this->adapter,
                        ]
                    )
                );
            }
        } else {
            //echo "entered else";
            return new HtmlResponse(
                $this->template->render(
                    'app::work::manage_work',
                    [
                    'rows' => $paginator,
                    'previous' => $pgs['prev'],
                    'next' => $pgs['nxt'],
                    'countp' => $pgs['cp'],
                    'searchParams' => $searchParams,
                    'carat' => $characs,
                    'request' => $request,
                    'adapter' => $this->adapter,
                    ]
                )
            );
        }
    }
}
