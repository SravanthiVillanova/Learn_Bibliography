<?php

namespace App\Action\Classification;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Paginator;

class ManageClassificationAction
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

    protected function getPaginator($query, $post)
    {
        if (!empty($query['action'])) {
            //manage classification hierarchy
            if ($query['action'] == 'get_children') {
                $table = new \App\Db\Table\Folder($this->adapter);
                $rows = $table->getChild($query['id']);
                $paginator = new Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($rows));

                return $paginator;
            }
            //view link click
            if ($query['action'] == 'get_siblings') {
                $table = new \App\Db\Table\Folder($this->adapter);
                $rows = $table->findParent();
                //$paginator = new Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($rows));
                return $rows;
            }
        }
        if (!empty($post['action'])) {
            //add folder
            if ($post['action'] == 'new') {
                if ($post['submit'] == 'Save') {
                    //echo "<pre>";print_r($post);echo "</pre>"; die();
                    $table = new \App\Db\Table\Folder($this->adapter);
                    $table->insertRecords($post['parent_id'], $post['new_classif_engtitle'], $post['new_classif_frenchtitle'],
                                $post['new_classif_germantitle'], $post['new_classif_dutchtitle'], $post['new_classif_spanishtitle'],
                                $post['new_classif_italiantitle'], $post['new_classif_sortorder']);
                }
            }
            //edit folder
            if ($post['action'] == 'edit') {
                if ($post['submit'] == 'Save') {
                    if (!is_null($post['id'])) {
                        //echo "<pre>";print_r($post);echo "</pre>"; die();
                        $table = new \App\Db\Table\Folder($this->adapter);
                        $table->updateRecord($post['id'], $post['edit_texten'], $post['edit_textfr'],
                                            $post['edit_textde'], $post['edit_textnl'], $post['edit_textes'],
                                            $post['edit_textit'], $post['edit_sortorder']);
                    }
                }
            }
            //move folder
            if ($post['action'] == 'move') {
                if ($post['submit_save'] == 'Save') {
                    $lg = count($post['select_fl']);
                    if ($post['select_fl'][$lg - 1] == '' || $post['select_fl'][$lg - 1] == 'none') {
                        $fl_to_move = $post['select_fl'][$lg - 2];
                    } else {
                        $fl_to_move = $post['select_fl'][$lg - 1];
                    }

                    $table = new \App\Db\Table\Folder($this->adapter);
                    $table->moveFolder($post['id'], $fl_to_move);
                }
            }
            //merge folder
            if ($post['action'] == 'merge_classification') {
                if ($post['submit_save'] == 'Save') {
                    $src_cnt = count($post['select_source_fl']);
                    $dst_cnt = count($post['select_dest_fl']);

                    if ($post['select_source_fl'][$src_cnt - 1] == '') {
                        $source_id = $post['select_source_fl'][$src_cnt - 2];
                    } else {
                        $source_id = $post['select_source_fl'][$src_cnt - 1];
                    }

                    if ($post['select_dest_fl'][$dst_cnt - 1] == '') {
                        $dest_id = $post['select_dest_fl'][$dst_cnt - 2];
                    } else {
                        $dest_id = $post['select_dest_fl'][$dst_cnt - 1];
                    }
                    // Move children
                    $table = new \App\Db\Table\Folder($this->adapter);
                    $table->mergeFolder($source_id, $dest_id);
    
                    //first delete potential duplicates to avoid key violation
                    $table = new \App\Db\Table\Work_Folder($this->adapter);
                    $table->mergeWkFlDelete($source_id, $dest_id);

                    // Move works
                    $table = new \App\Db\Table\Work_Folder($this->adapter);
                    $table->mergeWkFlUpdate($source_id, $dest_id);

                    // Track merge history -- update any previous history, then add the current merge:
                    $table = new \App\Db\Table\Folder_Merge_History($this->adapter);
                    $table->mergeFlMgHistUpdate($source_id, $dest_id);

                    // Track merge history -- update any previous history, then add the current merge:
                    $table = new \App\Db\Table\Folder_Merge_History($this->adapter);
                    $table->insertRecord($source_id, $dest_id);

                    // Purge
                    $table = new \App\Db\Table\Folder($this->adapter);
                    $table->mergeDelete($source_id);
                }
            }
            //Cancel
            if ($post['submit_cancel'] == 'Cancel') {
                $table = new \App\Db\Table\Folder($this->adapter);
                $paginator = $table->findParent();
                
                return $paginator;
            }
        }
        // default: blank for listing in manage
        $table = new \App\Db\Table\Folder($this->adapter);
        $paginator = $table->findParent();

        return $paginator;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $query = $request->getqueryParams();
        //print_r($query);
        $post = [];
        if ($request->getMethod() == 'POST') {
            $post = $request->getParsedBody();
        }
        $paginator = $this->getPaginator($query, $post);
        $paginator->setDefaultItemCountPerPage(15);
        $allItems = $paginator->getTotalItemCount();
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
        if (!empty($query['id']) && !empty($query['fl']) && $query['action'] == 'get_children') {
            $searchParams[] = 'id='.urlencode($query['id']).'&fl='.urlencode($query['fl']).'&action=get_children';
        }

        $ts = [];
        if (isset($query['action'])) {
            //get folder name for bread crumb
        if ($query['action'] == 'get_children') {
            $table = new \App\Db\Table\Folder($this->adapter);
            $r = $table->getTrail($query['id'], '');
            $r = $query['fl'].$r;

            $ts = explode(':', $r);
            $ts = array_reverse($ts);
        }
        }

        return new HtmlResponse(
            $this->template->render(
                'app::classification::manage_classification',
                [
                    'rows' => $paginator,
                    'previous' => $previous,
                    'next' => $next,
                    'countp' => $countPages,
                    //'previous_folder' => $previous_folder,
                    'trail' => $ts,
                    'searchParams' => implode('&', $searchParams),
                ]
            )
        );
    }
}
