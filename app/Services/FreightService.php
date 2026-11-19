<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Freight;
use App\Repositories\AddressRepository;
use App\Repositories\CarrierRepository;
use App\Repositories\FreightRepository;
use App\Repositories\ProductRepository;
use InvalidArgumentException;

final class FreightService
{
    public function __construct(
        private FreightRepository $freights,
        private ProductRepository $products,
        private AddressRepository $addresses,
        private CarrierRepository $carriers,
    ) {
    }

    public function save(array $data, ?int $id = null): int
    {
        $items = [];
        $productIds = $data['produtos'] ?? [];
        $quantities = $data['quantidades'] ?? [];
        foreach ((array) $productIds as $index => $productId) {
            $productId = (int) $productId;
            $quantity = (int) ($quantities[$index] ?? 0);
            if ($productId < 1 || $quantity < 1) {
                continue;
            }
            $items[$productId] = ($items[$productId] ?? 0) + $quantity;
        }
        $items = array_map(static fn (int $quantity, int $productId): array => [
            'produto_id' => $productId,
            'quantidade' => $quantity,
        ], $items, array_keys($items));

        $freightData = [
            'origem_id' => (int) ($data['origem_id'] ?? 0),
            'destino_id' => (int) ($data['destino_id'] ?? 0),
            'transportador_id' => (int) ($data['transportador_id'] ?? 0),
            'data_frete' => trim((string) ($data['data_frete'] ?? '')),
            'status' => trim((string) ($data['status'] ?? 'Em aberto')) ?: 'Em aberto',
            'items' => $items,
        ];
        $errors = Freight::validate($freightData);
        if ($freightData['data_frete'] !== '' && !$this->validDate($freightData['data_frete'])) {
            $errors['data_frete'] = 'Informe uma data válida.';
        }
        if (!$this->addresses->find($freightData['origem_id'])) {
            $errors['origem_id'] = 'O endereço de origem não existe.';
        }
        if (!$this->addresses->find($freightData['destino_id'])) {
            $errors['destino_id'] = 'O endereço de destino não existe.';
        }
        if (!$this->carriers->find($freightData['transportador_id'])) {
            $errors['transportador_id'] = 'O transportador não existe.';
        }
        foreach ($items as $item) {
            if (!$this->products->find((int) $item['produto_id'])) {
                $errors['items'] = 'Um dos produtos selecionados não existe.';
                break;
            }
        }
        if ($errors) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }

        unset($freightData['items']);
        return $this->freights->save($freightData, $items, $id);
    }

    private function validDate(string $date): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }
}

