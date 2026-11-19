<?php

declare(strict_types=1);

namespace App\Repositories;

final class AddressRepository extends BaseRepository
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT * FROM endereco';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE identificacao LIKE :search OR logradouro LIKE :search OR cidade LIKE :search OR cep LIKE :search';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY identificacao';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM endereco WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): int
    {
        if ($id) {
            $stmt = $this->pdo->prepare('UPDATE endereco SET identificacao = :identificacao, cep = :cep, logradouro = :logradouro, numero = :numero, complemento = :complemento, bairro = :bairro, cidade = :cidade, estado = :estado WHERE id = :id');
            $stmt->execute([...$data, 'id' => $id]);
            return $id;
        }
        $stmt = $this->pdo->prepare('INSERT INTO endereco (identificacao, cep, logradouro, numero, complemento, bairro, cidade, estado) VALUES (:identificacao, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :estado)');
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM endereco WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM endereco')->fetchColumn();
    }
}

