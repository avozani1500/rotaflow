<?php

declare(strict_types=1);

namespace App\Repositories;

final class CarrierRepository extends BaseRepository
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT * FROM transportador';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE nome LIKE :search OR documento LIKE :search OR telefone LIKE :search';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY nome';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM transportador WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): int
    {
        if ($id) {
            $stmt = $this->pdo->prepare('UPDATE transportador SET nome = :nome, documento = :documento, telefone = :telefone, email = :email, placa_veiculo = :placa_veiculo WHERE id = :id');
            $stmt->execute([...$data, 'id' => $id]);
            return $id;
        }
        $stmt = $this->pdo->prepare('INSERT INTO transportador (nome, documento, telefone, email, placa_veiculo) VALUES (:nome, :documento, :telefone, :email, :placa_veiculo)');
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM transportador WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM transportador')->fetchColumn();
    }
}

