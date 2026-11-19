<?php

declare(strict_types=1);

namespace App\Models;

final class Freight
{
    public const STATUSES = ['Em aberto', 'Em trânsito', 'Concluído', 'Cancelado'];

    public static function validate(array $data): array
    {
        $errors = [];
        foreach (['origem_id' => 'origem', 'destino_id' => 'destino', 'transportador_id' => 'transportador', 'data_frete' => 'data do frete'] as $field => $label) {
            if (empty($data[$field])) {
                $errors[$field] = 'Selecione ' . $label . '.';
            }
        }
        if (!empty($data['origem_id']) && $data['origem_id'] === $data['destino_id']) {
            $errors['destino_id'] = 'A origem e o destino precisam ser diferentes.';
        }
        if (!in_array($data['status'] ?? 'Em aberto', self::STATUSES, true)) {
            $errors['status'] = 'Situação do frete inválida.';
        }
        if (empty($data['items']) || !is_array($data['items'])) {
            $errors['items'] = 'Inclua pelo menos um produto no frete.';
        }

        return $errors;
    }
}

