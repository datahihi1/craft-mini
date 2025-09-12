<?php

use App\Controller\HomeController;
$router = new Craft\Application\Router();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
    $_SERVER['REQUEST_METHOD'] = strtoupper($_POST['_method']);
}

$router->get('/', [App\Controller\HomeController::class, 'index']);

$router->get('/test', [HomeController::class, 'test']);

$router->get('/hello/{name}', function ($name) : string {
    return "Hello, " . htmlspecialchars($name);
});
// Craft Router will report an error if we define the same route again. So we can test it with a different method.
// $router->get('/hello/{name}', function ($name) : string {
//     return "Hello, " . htmlspecialchars($name);
// });
$router->apiGet('/hello/{name}', function ($name) : string {
    return "Hello, " . htmlspecialchars($name);
});

// API routes for Users CRUD (query builder)
Craft\Application\Router::apiGet('/users', [HomeController::class, 'usersIndex']);
Craft\Application\Router::apiPost('/users', [HomeController::class, 'usersStore']);
Craft\Application\Router::apiPut('/users/{id}', [HomeController::class, 'usersUpdate']);
Craft\Application\Router::apiDelete('/users/{id}', [HomeController::class, 'usersDestroy']);

$router->runInstance();