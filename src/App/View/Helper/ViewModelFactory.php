<?php

namespace App\View\Helper;

use Zend\View\Helper\ViewModel;

class ViewModelFactory
{
    public function __invoke()
    {
        $model = new ViewModel();
        $model->setRoot(new \Zend\View\Model\ViewModel());

        return $model;
    }
}
