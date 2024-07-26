<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Service\Api;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/variations', function ($request, $response, $args) {
    $api = new Api();
    $results = $api->updateProducts();
    $response->getBody()->write(json_encode($results));
    return $response->withHeader('Content-Type', 'application/json');

});

$app->get('/products', function ($request, $response, $args) {
    $api = new Api();
    $results = $api->getProductIds();
    $response->getBody()->write(json_encode($results));
    return $response->withHeader('Content-Type', 'application/json');

});


$app->run();
