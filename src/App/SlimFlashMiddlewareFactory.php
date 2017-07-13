<?php

namespace App;

use Slim\Flash\Messages;

class SlimFlashMiddlewareFactory
{
    public function __invoke($container)
    {
        return function ($request, $response, $next) {
            // Start the session whenever we use this!
            session_start();

            return $next(
               $request->withAttribute('flash', new Messages()),
              $response
            );
        };
    }
}
