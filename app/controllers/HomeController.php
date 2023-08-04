<?php

namespace App\Controllers;

use App\Controllers\Base\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController extends Controller
{
    public function welcome(Request $request, Response $response, array $args)
    {
        return $response
            ->withHeader('Location', route_name($request, 'welcome.phtml'))
            ->withStatus(302);
    }
}
