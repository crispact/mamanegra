<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/../config/container.php');
$container = $builder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->add(TwigMiddleware::createFromContainer($app, Twig::class));

$debug = (bool) ($_ENV['APP_DEBUG'] ?? false);

$errorMiddleware = $app->addErrorMiddleware($debug, true, true);

$errorMiddleware->setErrorHandler(
    \Slim\Exception\HttpNotFoundException::class,
    function () use ($container): \Psr\Http\Message\ResponseInterface {
        $twig     = $container->get(Twig::class);
        $response = new \Slim\Psr7\Response();
        return $twig->render($response->withStatus(404), '404.html.twig');
    }
);

require __DIR__ . '/routes.php';

return $app;