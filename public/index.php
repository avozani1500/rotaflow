<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

$route = trim((string) ($_GET['route'] ?? 'dashboard'), '/');
$routes = [
    'dashboard' => [App\Controllers\DashboardController::class, 'index'],
    'products' => [App\Controllers\ProductController::class, 'index'],
    'products/save' => [App\Controllers\ProductController::class, 'save'],
    'products/delete' => [App\Controllers\ProductController::class, 'delete'],
    'addresses' => [App\Controllers\AddressController::class, 'index'],
    'addresses/save' => [App\Controllers\AddressController::class, 'save'],
    'addresses/delete' => [App\Controllers\AddressController::class, 'delete'],
    'carriers' => [App\Controllers\CarrierController::class, 'index'],
    'carriers/save' => [App\Controllers\CarrierController::class, 'save'],
    'carriers/delete' => [App\Controllers\CarrierController::class, 'delete'],
    'freights' => [App\Controllers\FreightController::class, 'index'],
    'freights/create' => [App\Controllers\FreightController::class, 'create'],
    'freights/save' => [App\Controllers\FreightController::class, 'save'],
    'freights/show' => [App\Controllers\FreightController::class, 'show'],
    'reports' => [App\Controllers\ReportController::class, 'index'],
    'reports/csv' => [App\Controllers\ReportController::class, 'csv'],
    'reports/pdf' => [App\Controllers\ReportController::class, 'pdf'],
];

if (!isset($routes[$route])) {
    http_response_code(404);
    exit('Página não encontrada.');
}

try {
    [$controller, $action] = $routes[$route];
    (new $controller())->$action();
} catch (PDOException $exception) {
    http_response_code(503);
    render('layouts/error', ['message' => 'Não foi possível conectar ao banco de dados. Importe o database/schema.sql e confira config/database.local.php.']);
} catch (Throwable $exception) {
    http_response_code(500);
    render('layouts/error', ['message' => 'Ocorreu um erro inesperado. Revise os dados e tente novamente.']);
}

