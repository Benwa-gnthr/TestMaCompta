<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = AppFactory::create();

$app->get('/comptes/{uuid}/ecritures', function (Request $request, Response $response, $args) {

    $uuid = $args['uuid'];

    $ecritures = [
        ['label' => 'Libellé de test 1'],
        ['label' => 'Libellé de test 2']
    ];

    $responseData = ['items' => $ecritures];
    $response->getBody()->write(json_encode($responseData));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->run(); 