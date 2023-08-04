<?php

namespace App\Controllers\Base;

use App\Foundation\View;
use App\Foundation\Database\Query;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Controller
{
    protected ?Query $query;
    protected ?Twig $twig;
    protected ?View $view;

    public function __construct()
    {
        $this->query = new Query;
        $this->view = new View;
    }

    /**
     * Twig View
     * 
     * @param Request $request
     * @return Twig 
     */
    protected function get(Request $request): Twig
    {
        $view = Twig::fromRequest($request);
        return $view;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $routeName
     * @param array $queryParams
     * @param array $data
     * @return mixed
     */
    protected function routeRedirect(Request $request, Response $response, string $routeName, array $queryParams = [], array $data = [])
    {
        return $response
            ->withHeader('Location', route_name($request, $routeName, $data, $queryParams))
            ->withStatus(302);
    }
}
