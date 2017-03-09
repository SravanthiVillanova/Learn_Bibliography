<?php
use Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware;

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
            App\Action\PingAction::class => App\Action\PingAction::class,
        ],
        'factories' => [	
            App\Action\HomePageAction::class => App\Action\HomePageFactory::class,
            //App\Action\LoginPageAction::class => App\Action\LoginPageFactory::class,
            App\Action\DefaultPageAction::class => App\Action\DefaultPageFactory::class,
            			
			/*App\Action\SimpleRenderAction::class => App\Action\Work\NewWorkFactory::class,
			App\Action\SimpleRenderAction::class => App\Action\Work\SearchWorkFactory::class,
			App\Action\Work\ManageWorkAction::class => App\Action\Work\ManageWorkFactory::class,
			App\Action\SimpleRenderAction::class => App\Action\Work\ReviewWorkFactory::class,
			App\Action\SimpleRenderAction::class => App\Action\Work\ClassifyWorkFactory::class,*/
			App\Action\Work\NewWorkAction::class => App\Action\Work\NewWorkFactory::class,
			App\Action\Work\SearchWorkAction::class => App\Action\Work\SearchWorkFactory::class,
			App\Action\Work\ManageWorkAction::class => App\Action\Work\ManageWorkFactory::class,
			App\Action\Work\ReviewWorkAction::class => App\Action\Work\ReviewWorkFactory::class,
			App\Action\Work\ClassifyWorkAction::class => App\Action\Work\ClassifyWorkFactory::class,
            
			App\Action\WorkType\NewWorkTypeAction::class => App\Action\WorkType\NewWorkTypeFactory::class,
			App\Action\WorkType\ManageWorkTypeAction::class => App\Action\WorkType\ManageWorkTypeFactory::class,
			App\Action\WorkType\EditWorkTypeAction::class => App\Action\WorkType\EditWorkTypeFactory::class,  
			App\Action\WorkType\DeleteWorkTypeAction::class => App\Action\WorkType\DeleteWorkTypeFactory::class, 
			App\Action\WorkType\ManageWorkTypeAttributeAction::class => App\Action\WorkType\ManageWorkTypeAttributeFactory::class,
			App\Action\WorkType\AttributesWorkTypeAction::class => App\Action\WorkType\AttributesWorkTypeFactory::class,
			App\Action\WorkType\NewAttributeAction::class => App\Action\WorkType\NewAttributeFactory::class,
			App\Action\WorkType\EditAttributeAction::class => App\Action\WorkType\EditAttributeFactory::class,
			App\Action\WorkType\DeleteAttributeAction::class => App\Action\WorkType\DeleteAttributeFactory::class,
			App\Action\WorkType\AttributeManageOptionsAction::class => App\Action\WorkType\AttributeManageOptionsFactory::class,
			App\Action\WorkType\NewOptionAction::class => App\Action\WorkType\NewOptionFactory::class,
			App\Action\WorkType\EditOptionAction::class => App\Action\WorkType\EditOptionFactory::class,
			App\Action\WorkType\DeleteOptionAction::class => App\Action\WorkType\DeleteOptionFactory::class,
			App\Action\WorkType\MergeDuplicateOptionAction::class => App\Action\WorkType\MergeDuplicateOptionFactory::class,
            
			App\Action\Classification\NewClassificationAction::class => App\Action\Classification\NewClassificationFactory::class,
			App\Action\Classification\ManageClassificationAction::class => App\Action\Classification\ManageClassificationFactory::class,
			App\Action\Classification\MergeClassificationAction::class => App\Action\Classification\MergeClassificationFactory::class,
			App\Action\Classification\ExportListClassificationAction::class => App\Action\Classification\ExportListClassificationFactory::class,
            
			/*App\Action\SimpleRenderAction::class => App\Action\Agent\NewAgentFactory::class,
			App\Action\SimpleRenderAction::class => App\Action\Agent\FindAgentFactory::class,			
            App\Action\SimpleRenderAction::class => App\Action\Agent\EditAgentFactory::class,
            App\Action\SimpleRenderAction::class => App\Action\Agent\DeleteAgentFactory::class,
			App\Action\SimpleRenderAction::class => App\Action\Agent\MergeAgentFactory::class,
			App\Action\Agent\ManageAgentAction::class => App\Action\Agent\ManageAgentFactory::class,	*/		
			App\Action\Agent\NewAgentAction::class => App\Action\Agent\NewAgentFactory::class,
			App\Action\Agent\FindAgentAction::class => App\Action\Agent\FindAgentFactory::class,
			App\Action\Agent\ManageAgentAction::class => App\Action\Agent\ManageAgentFactory::class,
            App\Action\Agent\EditAgentAction::class => App\Action\Agent\EditAgentFactory::class,
            App\Action\Agent\DeleteAgentAction::class => App\Action\Agent\DeleteAgentFactory::class,
			App\Action\Agent\MergeAgentAction::class => App\Action\Agent\MergeAgentFactory::class,
            
			App\Action\AgentType\NewAgentTypeAction::class => App\Action\AgentType\NewAgentTypeFactory::class,
			App\Action\AgentType\ManageAgentTypeAction::class => App\Action\AgentType\ManageAgentTypeFactory::class,
            App\Action\AgentType\EditAgentTypeAction::class => App\Action\AgentType\EditAgentTypeFactory::class,
            App\Action\AgentType\DeleteAgentTypeAction::class => App\Action\AgentType\DeleteAgentTypeFactory::class,
            
			App\Action\Publisher\NewPublisherAction::class => App\Action\Publisher\NewPublisherFactory::class,
			App\Action\Publisher\FindPublisherAction::class => App\Action\Publisher\FindPublisherFactory::class,
			App\Action\Publisher\ManagePublisherAction::class => App\Action\Publisher\ManagePublisherFactory::class,
            App\Action\Publisher\AddPublisherLocationAction::class => App\Action\Publisher\AddPublisherLocationFactory::class,
            App\Action\Publisher\DeleteMergePublisherLocationAction::class => App\Action\Publisher\DeleteMergePublisherLocationFactory::class,
            App\Action\Publisher\ManagePublisherLocationAction::class => App\Action\Publisher\ManagePublisherLocationFactory::class,
            App\Action\Publisher\EditPublisherAction::class => App\Action\Publisher\EditPublisherFactory::class,
            App\Action\Publisher\DeletePublisherAction::class => App\Action\Publisher\DeletePublisherFactory::class,
			App\Action\Publisher\MergePublisherAction::class => App\Action\Publisher\MergePublisherFactory::class,
            
			App\Action\Language\NewLanguageAction::class => App\Action\Language\NewLanguageFactory::class,
			App\Action\Language\ManageLanguageAction::class => App\Action\Language\ManageLanguageFactory::class,
            App\Action\Language\EditLanguageAction::class => App\Action\Language\EditLanguageFactory::class,
            App\Action\Language\DeleteLanguageAction::class => App\Action\Language\DeleteLanguageFactory::class,
            
			App\Action\Users\NewUserAction::class => App\Action\Users\NewUserFactory::class,
			App\Action\Users\ManageUsersAction::class => App\Action\Users\ManageUsersFactory::class,
			App\Action\Users\EditUserAction::class => App\Action\Users\EditUserFactory::class,
			App\Action\Users\DeleteUserAction::class => App\Action\Users\DeleteUserFactory::class,
			App\Action\Users\AccessUsersAction::class => App\Action\Users\AccessUsersFactory::class,
            
			App\Action\Preferences\ChangePasswordPreferencesAction::class => App\Action\Preferences\ChangePasswordPreferencesFactory::class,
        ],
    ],

    'routes' => [
        [
            'name' => 'home',
            'path' => '/',
            'middleware' => [
				\App\Middleware\AuthenticationMiddleware::class,
                BodyParamsMiddleware::class,
                App\Action\HomePageAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],     
		
        [
            'name' => 'default',
            'path' => '/default_latest',
            'middleware' => [				
                //BodyParamsMiddleware::class,
                App\Action\DefaultPageAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
        
        [
           'name' => 'new_work',
		    'path' => '/Work/new',
            'middleware' => [
				\App\Middleware\AuthenticationMiddleware::class,
                //BodyParamsMiddleware::class,
                App\Action\Work\NewWorkAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],

        [
            'name' => 'search_work',
		    'path' => '/Work/search',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Work\SearchWorkAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'manage_work',
		    'path' => '/Work/manage',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Work\ManageWorkAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'review_work',
		    'path' => '/Work/review',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Work\ReviewWorkAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'classify_work',
		    'path' => '/Work/classify',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Work\ClassifyWorkAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'new_worktype',
		    'path' => '/WorkType/new',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\NewWorkTypeAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'manage_worktype',
		    'path' => '/WorkType/manage',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\ManageWorkTypeAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'edit_worktype',
		    'path' => '/WorkType/edit',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\EditWorkTypeAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],   
		
		[
            'name' => 'delete_worktype',
		    'path' => '/WorkType/delete',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\DeleteWorkTypeAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'manage_worktypeattribute',
		    'path' => '/WorkType/manage_attribute',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\ManageWorkTypeAttributeAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'attributes_worktype',
		    'path' => '/WorkType/attributes',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\AttributesWorkTypeAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'new_attribute',
		    'path' => '/WorkType/new_attribute',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\NewAttributeAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'edit_attribute',
		    'path' => '/WorkType/edit_attribute',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\EditAttributeAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'delete_attribute',
		    'path' => '/WorkType/delete_attribute',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\DeleteAttributeAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'manage_attribute_options',
		    'path' => '/WorkType/manage_attribute_options',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\AttributeManageOptionsAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'new_option',
		    'path' => '/WorkType/new_option',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\NewOptionAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'edit_option',
		    'path' => '/WorkType/edit_option',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\EditOptionAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'delete_option',
		    'path' => '/WorkType/delete_option',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\DeleteOptionAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'merge_duplicate_option',
		    'path' => '/WorkType/merge_duplicate_option',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\WorkType\MergeDuplicateOptionAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'new_classification',
		    'path' => '/Classification/new',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Classification\NewClassificationAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'manage_classification',
		    'path' => '/Classification/manage',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Classification\ManageClassificationAction::class,
            ],
            'allowed_methods' => ['GET', 'POST'],
        ],
		
		[
            'name' => 'merge_classification',
		    'path' => '/Classification/merge',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Classification\MergeClassificationAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'exportlist_classification',
		    'path' => '/Classification/export',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Classification\ExportListClassificationAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'new_agent',
		    'path' => '/Agent/new',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Agent\NewAgentAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'find_agent',
		    'path' => '/Agent/find',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Agent\FindAgentAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'manage_agent',
		    'path' => '/Agent/manage',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Agent\ManageAgentAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
        
        [
            'name' => 'edit_agent',
		    'path' => '/Agent/edit',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Agent\EditAgentAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
        
		[
            'name' => 'delete_agent',
		    'path' => '/Agent/delete',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Agent\DeleteAgentAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'merge_agent',
		    'path' => '/Agent/merge',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Agent\MergeAgentAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'new_agenttype',
		    'path' => '/AgentType/new',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\AgentType\NewAgentTypeAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'manage_agenttype',
		    'path' => '/AgentType/manage',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\AgentType\ManageAgentTypeAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
        [
            'name' => 'edit_agenttype',
		    'path' => '/AgentType/edit',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\AgentType\EditAgentTypeAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
        
		[
            'name' => 'delete_agenttype',
		    'path' => '/AgentType/delete',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\AgentType\DeleteAgentTypeAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
        
		[
            'name' => 'new_publisher',
		    'path' => '/Publisher/newpublisher',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Publisher\NewPublisherAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'find_publisher',
		    'path' => '/Publisher/findpublisher',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Publisher\FindPublisherAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'manage_publisher',
		    'path' => '/Publisher/managepublisher[/page/{page}]',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Publisher\ManagePublisherAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
        
        [
            'name' => 'add_publisher_location',
		    'path' => '/Publisher/new_location',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Publisher\AddPublisherLocationAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
        
        [
            'name' => 'delete_merge_publisher_location',
		    'path' => '/Publisher/delete_merge_location',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Publisher\DeleteMergePublisherLocationAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
        
		[
            'name' => 'manage_publisherlocation',
		    'path' => '/Publisher/manage_location',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Publisher\ManagePublisherLocationAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
        
        [
            'name' => 'edit_publisher',
		    'path' => '/Publisher/edit_publisher',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Publisher\EditPublisherAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
        
        [
            'name' => 'delete_publisher',
		    'path' => '/Publisher/delete_publisher',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Publisher\DeletePublisherAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
        
		[
            'name' => 'merge_publisher',
		    'path' => '/Publisher/merge',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Publisher\MergePublisherAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'new_language',
		    'path' => '/Language/new',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Language\NewLanguageAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'manage_language',
		    'path' => '/Language/manage[/page/{page}]',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Language\ManageLanguageAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
        
        [
            'name' => 'edit_language',
		    'path' => '/Language/edit',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Language\EditLanguageAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
        
		[
            'name' => 'delete_language',
		    'path' => '/Language/delete',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Language\DeleteLanguageAction::class,
            ],                
            'allowed_methods' => ['GET','POST'],
        ],
        
		[
            'name' => 'new_user',
		    'path' => '/Users/new',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Users\NewUserAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'manage_users',
		    'path' => '/Users/manage',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Users\ManageUsersAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'edit_user',
		    'path' => '/Users/edit',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Users\EditUserAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'delete_user',
		    'path' => '/Users/delete',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Users\DeleteUserAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'access_users',
		    'path' => '/Users/access',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Users\AccessUsersAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
		
		[
            'name' => 'changepassword_preferences',
		    'path' => '/Preferences/changepassword',
            'middleware' => [
                //BodyParamsMiddleware::class,
                App\Action\Preferences\ChangePasswordPreferencesAction::class,
            ],
            'allowed_methods' => ['GET','POST'],
        ],
    ],
];
