<?php

declare(strict_types=1);

final class GalleryRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function paginate(int $page, int $perPage, string $search = '', string $format = 'all'): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        [$whereSql, $params] = $this->buildFilters($search, $format);

        $countStatement = $this->connection->prepare("SELECT COUNT(*) FROM fotos {$whereSql}");
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $sql = "SELECT * FROM fotos {$whereSql} ORDER BY updated_at DESC, id DESC LIMIT :limit OFFSET :offset";
        $statement = $this->connection->prepare($sql);

        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }

        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'items' => $statement->fetchAll(),
            'total' => $total,
            'pages' => max(1, (int) ceil($total / $perPage)),
            'page' => $page,
        ];
    }

    public function find(int $id): ?array
    {
        $statement = $this->connection->prepare('SELECT * FROM fotos WHERE id = :id LIMIT 1');
        $statement->execute([':id' => $id]);
        $photo = $statement->fetch();

        return $photo ?: null;
    }

    public function updateMetadata(int $id, string $title, string $description): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE fotos SET titulo = :title, text = :description WHERE id = :id'
        );

        return $statement->execute([
            ':id' => $id,
            ':title' => $title,
            ':description' => $description,
        ]);
    }

    public function create(string $title, string $filename, string $description): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO fotos (titulo, imagen, text) VALUES (:title, :image, :description)'
        );

        $statement->execute([
            ':title' => $title,
            ':image' => $filename,
            ':description' => $description,
        ]);

        return (int) $this->connection->lastInsertId('fotos_id_seq');
    }

    public function findRelated(int $currentId, int $limit = 4): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM fotos WHERE id <> :id ORDER BY updated_at DESC, id DESC LIMIT :limit'
        );
        $statement->bindValue(':id', $currentId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function latest(int $limit = 1): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM fotos ORDER BY updated_at DESC, id DESC LIMIT :limit'
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    private function buildFilters(string $search, string $format): array
    {
        $conditions = [];
        $params = [];

        $search = trim($this->normalizeSearch($search));
        if ($search !== '') {
            $conditions[] = '(LOWER(titulo) LIKE :search OR LOWER(text) LIKE :search OR LOWER(imagen) LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if ($format === 'png') {
            $conditions[] = "LOWER(SPLIT_PART(imagen, '.', 2)) = 'png'";
        }

        if ($format === 'jpeg') {
            $conditions[] = "LOWER(SPLIT_PART(imagen, '.', 2)) IN ('jpg', 'jpeg')";
        }

        if ($conditions === []) {
            return ['', $params];
        }

        return [' WHERE ' . implode(' AND ', $conditions), $params];
    }

    private function normalizeSearch(string $search): string
    {
        if (function_exists('mb_strtolower')) {
            return mb_strtolower($search, 'UTF-8');
        }

        return strtolower($search);
    }
}
