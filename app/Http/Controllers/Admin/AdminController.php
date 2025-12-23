<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Psr\Http\Message\ResponseInterface;

class AdminController extends Controller
{
    public function __invoke($request, $response): ResponseInterface
    {
        return $this->render($response, 'admin/admin_index.twig');
    }

}