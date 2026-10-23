<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Post
{
    public static function findBySlug(PDO $pdo, string $slug): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function random(PDO $pdo, int $limit): array
    {
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE op1 IS NOT NULL ORDER BY RAND() LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function search(PDO $pdo, string $query): array
    {
        $term = '%' . $query . '%';
        $stmt = $pdo->prepare(
            'SELECT * FROM posts WHERE body LIKE ? OR title LIKE ?'
        );
        $stmt->execute([$term, $term]);
        return $stmt->fetchAll();
    }
}