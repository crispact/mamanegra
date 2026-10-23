<?php

declare(strict_types=1);

use App\Controllers\HomeController;

$app->get('/',                               [HomeController::class, 'index']);
$app->get('/latacunga',                      [HomeController::class, 'latacunga']);
$app->get('/volcan-cotopaxi',                [HomeController::class, 'volcanCotopaxi']);
$app->get('/atractivos',                     [HomeController::class, 'atractivos']);
$app->get('/la-comparsa-de-la-mama-negra',   [HomeController::class, 'comparsa']);
$app->get('/contacto',                       [HomeController::class, 'contacto']);
$app->get('/search',                         [HomeController::class, 'search']);
$app->get('/personajes/{slug}',              [HomeController::class, 'showPost']);
$app->get('/la-comparsa/{slug}',             [HomeController::class, 'showPost']);