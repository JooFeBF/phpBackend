<?php

// Activate strict typing
declare(strict_types=1);


use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;
use App\Middleware\AddJsonResponseHeader;
use App\Controllers\Products;
use App\Middleware\GetProduct;
use Slim\Routing\RouteCollectorProxy;

// Autoload dependencies using Composer
define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . "/vendor/autoload.php";

// Create a DI container
$builder = new ContainerBuilder();

$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs());

$app->addBodyParsingMiddleware();

// The true parameters in addErrorMiddleware  are for displaying error details, logging errors, and logging error details
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Force the content type to be JSON for all error responses
$errorHandler = $errorMiddleware->getDefaultErrorHandler();

$errorHandler->forceContentType('application/json');

$app->add(new AddJsonResponseHeader);

$app->group('/api', function (RouteCollectorProxy $group) {

  $group->get('/products', Products::class);

  $group->post('/products', Products::class . ':createProduct');

  $group->group('', function (RouteCollectorProxy $group) {
    $group->get('/products/{id:[0-9]+}', Products::class . ':getProduct');

    $group->patch('/products/{id:[0-9]+}', Products::class . ':updateProduct');

    $group->delete('/products/{id:[0-9]+}', Products::class . ':deleteProduct');
  })->add(GetProduct::class);

});

$app->run();

