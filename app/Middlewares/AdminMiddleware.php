<?php

namespace App\Middlewares;

use Slim\Exception\HttpForbiddenException;

class AdminMiddleware
{
    public function __invoke($request, $handler)
    {
        if(!isset($_SESSION['user']) || $_SESSION['user']->role !== "admin") {
            throw new HttpForbiddenException($request);
        }

        return $handler->handle($request);
    }
}