<?php

declare(strict_types=1);

namespace App\Repositories;

use DateTimeImmutable;
use PDO;

final class FreightRepository extends BaseRepository
{
    public function all(?string $from = null, ?string $to = null): array
    {
        $sql = 'SELECT f.*, o.identificacao AS origem_nome, o.cidade AS origem_cidade, o.estado AS origem_estado,
                       d.identificacao AS destino_nome, d.cidade AS destino_cidade, d.estado AS destino_estado,
                       t.nome AS transportador_nome,
                       COALESCE(SUM(fp.quantidade), 0) AS quantidade_total,
                       COALESCE(SUM(fp.quantidade * p.valor), 0) AS valor_total,
                       COUNT(fp.produto_id) AS tipos_produto
                FROM frete f
                INNER JOIN endereco o ON o.id = f.origem_id
                INNER JOIN endereco d ON d.id = f.destino_id
                INNER JOIN transportador t ON t.id = f.transportador_id
                LEFT JOIN frete_produto fp ON fp.frete_id = f.id
                LEFT JOIN produto p ON p.id = fp.produto_id';
        $where = [];
        $params = [];
        if ($from) {
            $where[] = 'f.data_frete >= :from_date';
            $params['from_date'] = $from;
        }
        if ($to) {
            $where[] = 'f.data_frete <= :to_date';
            $params['to_date'] = $to;
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' GROUP BY f.id ORDER BY f.data_frete DESC, f.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT f.*, o.identificacao AS origem_nome, o.cidade AS origem_cidade, o.estado AS origem_estado,
                                            d.identificacao AS destino_nome, d.cidade AS destino_cidade, d.estado AS destino_estado,
                                            t.nome AS transportador_nome, t.documento AS transportador_documento,
                                            t.telefone AS transportador_telefone, t.email AS transportador_email,
                                            t.placa_veiculo
                                     FROM frete f
                                     INNER JOIN endereco o ON o.id = f.origem_id
                                     INNER JOIN endereco d ON d.id = f.destino_id
                                     INNER JOIN transportador t ON t.id = f.transportador_id
                                     WHERE f.id = :id');
        $stmt->execute(['id' => $id]);
        $freight = $stmt->fetch();
        if (!$freight) {
            return null;
        }

        $items = $this->pdo->prepare('SELECT fp.produto_id, fp.quantidade, p.nome, p.peso, p.valor,
                                             (fp.quantidade * p.peso) AS peso_total,
                                             (fp.quantidade * p.valor) AS valor_total
                                      FROM frete_produto fp
                                      INNER JOIN produto p ON p.id = fp.produto_id
                                      WHERE fp.frete_id = :frete_id
                                      ORDER BY p.nome');
        $items->execute(['frete_id' => $id]);
        $freight['items'] = $items->fetchAll();

        return $freight;
    }

    public function save(array $data, array $items, ?int $id = null): int
    {
        $this->pdo->beginTransaction();
        try {
            if ($id) {
                $stmt = $this->pdo->prepare('UPDATE frete SET origem_id = :origem_id, destino_id = :destino_id, transportador_id = :transportador_id, data_frete = :data_frete, status = :status WHERE id = :id');
                $stmt->execute([...$data, 'id' => $id]);
                $freightId = $id;
                $this->pdo->prepare('DELETE FROM frete_produto WHERE frete_id = :frete_id')->execute(['frete_id' => $id]);
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO frete (origem_id, destino_id, transportador_id, data_frete, status) VALUES (:origem_id, :destino_id, :transportador_id, :data_frete, :status)');
                $stmt->execute($data);
                $freightId = (int) $this->pdo->lastInsertId();
            }

            $itemStmt = $this->pdo->prepare('INSERT INTO frete_produto (frete_id, produto_id, quantidade) VALUES (:frete_id, :produto_id, :quantidade)');
            foreach ($items as $item) {
                $itemStmt->execute([
                    'frete_id' => $freightId,
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                ]);
            }

            $this->pdo->commit();
            return $freightId;
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM frete')->fetchColumn();
    }

    public function recent(int $limit = 5): array
    {
        $limit = max(1, min(20, $limit));
        return array_slice($this->all(null, null), 0, $limit);
    }

    public function totalsFromRows(array $freights): array
    {
        return [
            'freights' => count($freights),
            'quantity' => array_sum(array_map(static fn (array $row): int => (int) $row['quantidade_total'], $freights)),
            'value' => array_sum(array_map(static fn (array $row): float => (float) $row['valor_total'], $freights)),
        ];
    }
}

