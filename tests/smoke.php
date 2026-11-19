<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$required = [
    'bootstrap.php',
    'public/index.php',
    'config/database.example.php',
    'database/schema.sql',
    'database/seed.sql',
    'app/Controllers/FreightController.php',
    'app/Services/FreightService.php',
    'public/assets/js/freight.js',
];

$failures = [];
foreach ($required as $file) {
    if (!is_file($root . DIRECTORY_SEPARATOR . $file)) {
        $failures[] = 'Arquivo ausente: ' . $file;
    }
}

$schema = file_get_contents($root . '/database/schema.sql');
foreach (['CREATE TABLE produto', 'CREATE TABLE endereco', 'CREATE TABLE transportador', 'CREATE TABLE frete', 'CREATE TABLE frete_produto', 'FOREIGN KEY', 'PRIMARY KEY'] as $term) {
    if (!str_contains($schema, $term)) {
        $failures[] = 'Schema sem requisito: ' . $term;
    }
}

if ($failures) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo "Smoke test OK: arquivos e schema essenciais encontrados." . PHP_EOL;

