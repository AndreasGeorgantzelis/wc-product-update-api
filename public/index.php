<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Service\UpdateMetaData;
use Service\UpdateCategories;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$api = new \Service\Api();

$app->get('/', function ($request, $response, $args) {

    $response->getBody()->write('hello world');
    return $response->withHeader('Content-Type', 'application/json');

});

$app->get('/variations', function ($request, $response, $args) {
    $api = new UpdateMetaData();
    $results = $api->updateProducts();
    $response->getBody()->write(json_encode($results));
    return $response->withHeader('Content-Type', 'application/json');

});

$app->get('/products', function ($request, $response, $args) {
    $api = new UpdateMetaData();
    $results = $api->getProductIds();
    $response->getBody()->write(json_encode($results));
    return $response->withHeader('Content-Type', 'application/json');

});

$app->get('/categories', function ($request, $response, $args) {
    $api = new \Service\Api();
    $update = new UpdateCategories($api);
    $results = $update->updateProductCategories();
    $response->getBody()->write(json_encode($results));
    return $response->withHeader('Content-Type', 'application/json');

});


$app->run();
