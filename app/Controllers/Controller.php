<?php

declare(strict_types=1);

namespace App\Controllers;

use PDOException;

abstract class Controller
{
    protected function postData(array $fields): array
    {
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = trim((string) ($_POST[$field] ?? ''));
        }
        return $data;
    }

    protected function persistError(\Throwable $exception, string $fallback): never
    {
        set_old($_POST);
        $message = $exception instanceof PDOException && str_contains($exception->getMessage(), '23000')
            ? 'Não foi possível excluir ou salvar porque o registro possui relacionamentos.'
            : $fallback;
        flash('error', $message);
        redirect($_POST['_redirect'] ?? 'dashboard');
    }
}

