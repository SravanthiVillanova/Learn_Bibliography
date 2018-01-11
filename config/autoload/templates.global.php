<?php

return [
    'dependencies' => [
        'factories' => [
            'Zend\Expressive\FinalHandler' =>
                Zend\Expressive\Container\TemplatedErrorHandlerFactory::class,

            Zend\Expressive\Template\TemplateRendererInterface::class =>
                Zend\Expressive\ZendView\ZendViewRendererFactory::class,

            Zend\View\HelperPluginManager::class =>
                Zend\Expressive\ZendView\HelperPluginManagerFactory::class,
        ],
    ],

    'templates' => [
        'layout' => 'layout/default',
        'map' => [
            'layout/default' => 'templates/layout/default.phtml',
            'error/error'    => 'templates/error/error.phtml',
            'error/404'      => 'templates/error/404.phtml',
            //html templates
            //for login
            'vubib::default' => 'templates/vubib/default_latest.phtml',
            //'vubib::login' => 'templates/vubib/login-page.phtml',
            //work
            'vubib::work::new_work' => 'templates/vubib/work/new.phtml',
			'vubib::work::get_work_details' => 'templates/vubib/work/get_work_details.phtml',
            'vubib::work::manage_work' => 'templates/vubib/work/manage.phtml',
			'vubib::work::edit_work' => 'templates/vubib/work/edit.phtml',
			'vubib::work::delete_work' => 'templates/vubib/work/delete.phtml',
            'vubib::work::search_work' => 'templates/vubib/work/search.phtml',
            'vubib::work::review_work' => 'templates/vubib/work/review.phtml',
            'vubib::work::classify_work' => 'templates/vubib/work/classify.phtml',
            //work type
            'vubib::worktype::new_worktype' => 'templates/vubib/worktype/new.phtml',
            'vubib::worktype::manage_worktype' => 'templates/vubib/worktype/manage.phtml',
			'vubib::worktype::edit_worktype' => 'templates/vubib/worktype/edit.phtml',
			'vubib::worktype::delete_worktype' => 'templates/vubib/worktype/delete.phtml',
			'vubib::worktype::manage_worktypeattribute' => 'templates/vubib/worktype/manage_attributes.phtml',
            'vubib::worktype::attributes_worktype' => 'templates/vubib/worktype/attributes.phtml',
			'vubib::worktype::new_attribute' => 'templates/vubib/worktype/new_attribute.phtml',
			'vubib::worktype::edit_attribute' => 'templates/vubib/worktype/edit_attribute.phtml',
			'vubib::worktype::delete_attribute' => 'templates/vubib/worktype/delete_attribute.phtml',
			'vubib::worktype::manage_attribute_options' => 'templates/vubib/worktype/manage_attribute_options.phtml',
			'vubib::worktype::new_option' => 'templates/vubib/worktype/new_option.phtml',
			'vubib::worktype::edit_option' => 'templates/vubib/worktype/edit_option.phtml',
			'vubib::worktype::delete_option' => 'templates/vubib/worktype/delete_option.phtml',
			'vubib::worktype::merge_duplicate_option' => 'templates/vubib/worktype/merge_duplicate_values.phtml',
            //classification
            'vubib::classification::new_classification' => 'templates/vubib/classification/new.phtml',
            'vubib::classification::manage_classification' => 'templates/vubib/classification/manage.phtml',
            'vubib::classification::merge_classification' => 'templates/vubib/classification/merge.phtml',
            'vubib::classification::exportlist_classification' => 'templates/vubib/classification/exportlist.phtml',
			'vubib::classification::edit_classification' => 'templates/vubib/classification/edit.phtml',
            'vubib::classification::move_classification' => 'templates/vubib/classification/move.phtml',
            //agent
            'vubib::agent::new_agent' => 'templates/vubib/agent/new.phtml',
            'vubib::agent::find_agent' => 'templates/vubib/agent/find.phtml',
            'vubib::agent::manage_agent' => 'templates/vubib/agent/manage.phtml',
            'vubib::agent::edit_agent' => 'templates/vubib/agent/edit.phtml',
            'vubib::agent::delete_agent' => 'templates/vubib/agent/delete.phtml',
            'vubib::agent::merge_agent' => 'templates/vubib/agent/merge.phtml',
            //agent type
            'vubib::agenttype::new_agenttype' => 'templates/vubib/agenttype/new.phtml',
            'vubib::agenttype::manage_agenttype' => 'templates/vubib/agenttype/manage.phtml',
            'vubib::agenttype::edit_agenttype' => 'templates/vubib/agenttype/edit.phtml',
            'vubib::agenttype::delete_agenttype' => 'templates/vubib/agenttype/delete.phtml',     
             //publisher
            'vubib::publisher::new_publisher' => 'templates/vubib/publisher/new.phtml',
            'vubib::publisher::manage_publisher' => 'templates/vubib/publisher/manage.phtml',
            'vubib::publisher::edit_publisher' => 'templates/vubib/publisher/edit.phtml',
            'vubib::publisher::delete_publisher' => 'templates/vubib/publisher/delete.phtml',
            'vubib::publisher::find_publisher' => 'templates/vubib/publisher/find.phtml',
            'vubib::publisher::add_publisher_location' => 'templates/vubib/publisher/new_location.phtml',
            'vubib::publisher::delete_merge_publisher_location' => 'templates/vubib/publisher/delete_merge_location.phtml',
            'vubib::publisher::manage_publisherlocation' => 'templates/vubib/publisher/manage_location.phtml',
            'vubib::publisher::merge_publisher' => 'templates/vubib/publisher/merge.phtml',
            //language
            'vubib::language::new_language' => 'templates/vubib/language/new.phtml',
            'vubib::language::manage_language' => 'templates/vubib/language/manage.phtml',
            'vubib::language::edit_language' => 'templates/vubib/language/edit.phtml',
            'vubib::language::delete_language' => 'templates/vubib/language/delete.phtml',           
            //users
            'vubib::users::new_user' => 'templates/vubib/users/new.phtml',
            'vubib::users::manage_users' => 'templates/vubib/users/manage.phtml',
			'vubib::users::edit_user' => 'templates/vubib/users/edit.phtml',
			'vubib::users::delete_user' => 'templates/vubib/users/delete.phtml',
            'vubib::users::access_users' => 'templates/vubib/users/access.phtml',
            //preferences
            'vubib::preferences::changepassword_preferences' => 'templates/vubib/preferences/changepassword_preferences.phtml',
        ],
        'paths' => [
            'vubib'    => ['templates/vubib'],
            'layout' => ['templates/layout'],
            'error'  => ['templates/error'],
        ],
    ],

    'view_helpers' => [
        //new
        'invokables' => [
            Zend\View\Helper\BasePath::class => Blast\BaseUrl\BasePathViewHelperFactory::class,
        ],
        'factories' => [
            Zend\View\Helper\ViewModel::class => VuBib\View\Helper\ViewModelFactory::class,
			'isUser' => VuBib\View\Helper\IsUserFactory::class,
			Zend\Form\ConfigProvider::class => VuBib\FormHelpersMiddlewareFactory::class,
        ],
        // zend-servicemanager-style configuration for adding view helpers:
        // - 'aliases'
        // - 'invokables'
        // - 'factories'
        // - 'abstract_factories'
        // - etc.
    ],
];
