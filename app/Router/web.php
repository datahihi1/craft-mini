<?php

use App\Controller\HomeController;
$router = new Craft\Application\Router();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
    $_SERVER['REQUEST_METHOD'] = strtoupper($_POST['_method']);
}

$router->get('/', [App\Controller\HomeController::class, 'index']);

$router->get('/welcome', [HomeController::class, 'welcome']);

try {
    $router->runInstance();
} catch (Exception $e) {
    die($e->getMessage());
}