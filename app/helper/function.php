<?php

use App\Foundation\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

/* 
* @override Slim
*/

if (!function_exists('route_name')) {

    /**
     * #route_name($request, 'user.login');
     * 
     * @param Request $request
     * @param string $routeName
     * @param array $data
     * @param array $queryParams
     * @return string
     */
    function route_name(Request $request, string $routeName, array $data = [], array $queryParams = []): string
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $routeParser->urlFor($routeName, $data, $queryParams);
    }
}

if (!function_exists('base_uri')) {

    /**
     * #base_uri($request);
     *
     * @param Request $request
     * @return string
     */
    function base_uri(Request $request): string
    {
        $uriObj = $request->getUri();

        $fullURl = $uriObj->getScheme() . '://' . $uriObj->getHost() . ':' . $uriObj->getPort() .  $uriObj->getPath();
        return $fullURl;
    }
}

if (!function_exists('assets')) {

    /**
     * #assets($path_in_assets);
     * 
     * @param string $path
     * @return string
     * @throws Exception
     */
    function assets(string $path): string
    {
        $realPath = __DIR__ . "/../../assets/{$path}";

        if (file_exists($realPath)) {
            return file_get_contents($realPath);
        }
        throw new Exception("Not found: {$realPath}");
    }
}

if (!function_exists('throw_if')) {

    /**
     * @param bool $boolean
     * @param mixed $exception
     * @param string $message
     * @return void
     */
    function throw_if($boolean, $exception, $message = '')
    {
        if ($boolean) {
            throw (is_string($exception) ? new $exception($message) : $exception);
        }
    }
}

if (!function_exists('throw_unless')) {

    /**
     * @param bool $boolean
     * @param mixed $exception
     * @param string $message
     * @return void
     */
    function throw_unless($boolean, $exception, $message)
    {
        if (!$boolean) {
            throw (is_string($exception) ? new $exception($message) : $exception);
        }
    }
}

if (!function_exists('view')) {

    /**
     * @return View|void
     * @throws Exception
     */
    function view()
    {
        $view = new View();
        if ($view instanceof View) {
            return $view;
        }

        throw_if(false, 'Exception', 'View instance not found.');
    }
}

if (!function_exists('redirect')) {

    /**
     * @param Request $request
     * @param Response $response
     * @param string $routeName
     * @param array $queryParams
     * @param array $data
     * @return mixed
     */
    function redirect(Request $request, Response $response, string $routeName, array $queryParams = [], array $data = [])
    {
        return $response
            ->withHeader('Location', route_name($request, $routeName, $data, $queryParams))
            ->withStatus(302);
    }
}
