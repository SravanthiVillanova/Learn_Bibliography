<?php

return [
    'debug' => false,

    'config_cache_enabled' => false,

    'zend-expressive' => [
        'error_handler' => [
            'template_404'   => 'error::404',
            'template_error' => 'error::error',
        ],
    ],
	'app' => [
      'authentication' => [
          'default_redirect_to' => '/',
      ]
    ]
];
