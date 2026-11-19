<?php

declare(strict_types=1);

namespace App\Models;

final class Carrier
{
    public static function validate(array $data): array
    {
        $errors = [];
        foreach (['nome' => 'nome / razão social', 'documento' => 'CPF / CNPJ', 'telefone' => 'telefone'] as $field => $label) {
            if (trim((string) ($data[$field] ?? '')) === '') {
                $errors[$field] = 'Informe o ' . $label . '.';
            }
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Informe um e-mail válido.';
        }

        return $errors;
    }
}

