<?php

declare(strict_types=1);

namespace App\Repositories;

final class ProductRepository extends BaseRepository
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT * FROM produto';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE nome LIKE :search OR descricao LIKE :search';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY nome';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM produto WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): int
    {
        if ($id) {
            $stmt = $this->pdo->prepare('UPDATE produto SET nome = :nome, descricao = :descricao, peso = :peso, valor = :valor WHERE id = :id');
            $stmt->execute([...$data, 'id' => $id]);
            return $id;
        }
        $stmt = $this->pdo->prepare('INSERT INTO produto (nome, descricao, peso, valor) VALUES (:nome, :descricao, :peso, :valor)');
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM produto WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM produto')->fetchColumn();
    }
}

