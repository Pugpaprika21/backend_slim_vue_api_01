<?php

use App\Foundation\Database\Query;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '../../src/include/dependency.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

$query = new Query('mysql');

const AUTHORIZED = 'bWVldGluZy5ibWE6bWVldGluZy5iaXpwb3RlbnRpYWw=';

$app->get('/getUsers', function (Request $request, Response $response, array $args) use ($query) {

    $params = $request->getQueryParams();

    if (!isset($params['token'])) {
        throw new Exception('AUTHORIZED_ERROR', 1);
    }

    if ($params['token'] == AUTHORIZED) {

        $users = $query->table("user_tb")->where('user_status', '=', 'Y')->orderBy('create_date_at', 'desc')->get();

        $rows = count($users);

        if ($rows > 0) {
            $payload = json_encode(['data' => $users, 'status_bool' => true, 'rows' => $rows, 'params' => $params]);
            $response->getBody()->write($payload);
        } else {
            $payload = json_encode(['data' => $users, 'status_bool' => false, 'rows' => $rows]);
            $response->getBody()->write($payload);
        }
    } else {
        $payload = json_encode(['data' => [], 'status_bool' => false, 'massage' => 'AUTHORIZED_ERROR', 'rows' => 0]);
        $response->getBody()->write($payload);
    }

    return $response
        ->withHeader('Access-Control-Allow-Methods', 'GET')
        ->withHeader('Access-Control-Allow-Headers', '*')
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->get('/editUser/{userId}', function (Request $request, Response $response, array $args) use ($query) {

    $userId = str($args['userId']);
    $params = $request->getQueryParams();

    if (!isset($params['token'])) {
        throw new Exception('AUTHORIZED_ERROR', 1);
    }

    $user = $query->table("user_tb")->where('user_id', '=', $userId)->get();
    $rows = count($user);

    if ($params['token'] == AUTHORIZED) {
        $payload = json_encode(['data' => $user, 'status_bool' => true, 'rows' => $rows]);
        $response->getBody()->write($payload);
    } else {
        $payload = json_encode(['data' => [], 'status_bool' => false, 'rows' => $rows]);
        $response->getBody()->write($payload);
    }

    return $response
        ->withHeader('Access-Control-Allow-Methods', 'GET')
        ->withHeader('Access-Control-Allow-Headers', '*')
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->get('/updateUser/{userId}', function (Request $request, Response $response, array $args) use ($query) {

    $params = $request->getQueryParams();

    if (!isset($params['token'])) {
        throw new Exception('AUTHORIZED_ERROR', 1);
    }

    if ($params['token'] == AUTHORIZED) {

        if (!empty($params['user'])) {
            $body = $params['user'];
            $userId = str($args['userId']);

            $set = [
                'user_name' => str($body['user_name']),
                'user_email' => str($body['user_email']),
            ];

            $userUpdate = $query->table('user_tb')->fields($set)->where('user_id', '=', $userId)->update();
            if ($userUpdate) {

                $updateRec = $query->table('user_tb')->select('user_id', 'user_name', 'user_email')->where('user_id', '=', $userId)->get();
                $rows = count($updateRec);

                $payload = json_encode(['data' => $updateRec, 'status_bool' => true, 'rows' => $rows]);
                $response->getBody()->write($payload);
            } else {
                $payload = json_encode(['data' => [], 'status_bool' => false, 'rows' => 0]);
                $response->getBody()->write($payload);
            }
        } else {
            $payload = json_encode(['data' => [], 'status_bool' => false, 'rows' => 0]);
            $response->getBody()->write($payload);
        }
    } else {
        $payload = json_encode(['data' => [], 'status_bool' => false, 'rows' => 0]);
        $response->getBody()->write($payload);
    }

    return $response
        ->withHeader('Access-Control-Allow-Methods', '*')
        ->withHeader('Access-Control-Allow-Headers', '*')
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->delete('/deleteUser/{userId}', function (Request $request, Response $response, array $args) use ($query) {

    $payload = json_encode(['data' => [], 'status_bool' => false, 'rows' => 0]);
    $response->getBody()->write($payload);
    return $response
        ->withHeader('Access-Control-Allow-Methods', '*')
        ->withHeader('Access-Control-Allow-Headers', '*')
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->run();
