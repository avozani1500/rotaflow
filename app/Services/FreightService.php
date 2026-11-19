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
        $productIds = $data['produtos'] ?? [];
        $quantities = $data['quantidades'] ?? [];
        if (!is_array($productIds) || !is_array($quantities) || count($productIds) !== count($quantities) || count($productIds) === 0) {
            throw new InvalidArgumentException('Informe ao menos um produto com uma quantidade válida.');
        }

        $items = [];
        $selectedProducts = [];
        foreach (array_values($productIds) as $index => $rawProductId) {
            $rawQuantity = array_values($quantities)[$index] ?? null;
            if (!is_scalar($rawProductId) || !preg_match('/^[1-9]\d*$/', (string) $rawProductId)) {
                throw new InvalidArgumentException('Selecione produtos válidos para o frete.');
            }
            if (!is_scalar($rawQuantity) || !preg_match('/^[1-9]\d*$/', (string) $rawQuantity)) {
                throw new InvalidArgumentException('Informe quantidades inteiras maiores que zero.');
            }

            $productId = (int) $rawProductId;
            if (isset($selectedProducts[$productId])) {
                throw new InvalidArgumentException('Não é permitido selecionar o mesmo produto mais de uma vez.');
            }
            $selectedProducts[$productId] = true;
            $items[] = [
                'produto_id' => $productId,
                'quantidade' => (int) $rawQuantity,
            ];
        }

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

