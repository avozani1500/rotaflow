<?php

declare(strict_types=1);

namespace App\Models;

final class Address
{
    public static function validate(array $data): array
    {
        $required = [
            'identificacao' => 'identificação',
            'cep' => 'CEP',
            'logradouro' => 'logradouro',
            'numero' => 'número',
            'bairro' => 'bairro',
            'cidade' => 'cidade',
            'estado' => 'estado',
        ];
        $errors = [];
        foreach ($required as $field => $label) {
            if (trim((string) ($data[$field] ?? '')) === '') {
                $errors[$field] = 'Informe o ' . $label . '.';
            }
        }

        $cep = preg_replace('/\D/', '', (string) ($data['cep'] ?? ''));
        if ($cep !== '' && strlen($cep) !== 8) {
            $errors['cep'] = 'Informe um CEP com 8 dígitos.';
        }
        if (strlen((string) ($data['estado'] ?? '')) !== 2) {
            $errors['estado'] = 'Selecione uma UF válida.';
        }

        return $errors;
    }
}

