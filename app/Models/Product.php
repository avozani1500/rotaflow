<?php

declare(strict_types=1);

namespace App\Models;

final class Product
{
    public static function validate(array $data): array
    {
        $errors = [];
        $name = trim((string) ($data['nome'] ?? ''));
        $weight = (float) ($data['peso'] ?? 0);
        $value = (float) ($data['valor'] ?? -1);

        if ($name === '' || mb_strlen($name) > 150) {
            $errors['nome'] = 'Informe um nome de produto com até 150 caracteres.';
        }
        if ($weight <= 0) {
            $errors['peso'] = 'O peso deve ser maior que zero.';
        }
        if ($value < 0) {
            $errors['valor'] = 'O valor não pode ser negativo.';
        }

        return $errors;
    }
}

