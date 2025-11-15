<?php
namespace App\Models;

use PDO;

class Post {
    private static function connect(): PDO {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db = getenv('DB_NAME') ?: 'metro_web_class';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    }

    // Create a new post
    public static function create(int $userId, string $content, ?string $image = null): int {
        $stmt = self::connect()->prepare('INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $content, $image]);
        return (int)self::connect()->lastInsertId();
    }

    // Fetch all posts with user info
    public static function getAllWithUsers(): array {
        $stmt = self::connect()->prepare('
            SELECT p.*, u.name as user_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Delete post
    public static function delete(int $postId, int $userId): bool {
        $stmt = self::connect()->prepare('DELETE FROM posts WHERE id = ? AND user_id = ?');
        return $stmt->execute([$postId, $userId]);
    }

    // Likes
    public static function toggleLike(int $postId, int $userId): void {
        $pdo = self::connect();
        $exists = $pdo->prepare('SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?');
        $exists->execute([$postId, $userId]);
        if ($exists->fetch()) {
            $del = $pdo->prepare('DELETE FROM post_likes WHERE post_id = ? AND user_id = ?');
            $del->execute([$postId, $userId]);
        } else {
            $ins = $pdo->prepare('INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)');
            $ins->execute([$postId, $userId]);
        }
    }

    public static function getLikesCount(int $postId): int {
        $stmt = self::connect()->prepare('SELECT COUNT(*) as cnt FROM post_likes WHERE post_id = ?');
        $stmt->execute([$postId]);
        return (int)$stmt->fetch()['cnt'];
    }

    public static function userLiked(int $postId, int $userId): bool {
        $stmt = self::connect()->prepare('SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?');
        $stmt->execute([$postId, $userId]);
        return (bool)$stmt->fetch();
    }

    // Comments
    public static function getComments(int $postId, ?int $parentId = null): array {
        $stmt = self::connect()->prepare('
            SELECT c.*, u.name as user_name 
            FROM post_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ? AND '.($parentId === null ? 'c.parent_id IS NULL' : 'c.parent_id = ?').'
            ORDER BY c.created_at ASC
        ');
        $stmt->execute($parentId === null ? [$postId] : [$postId, $parentId]);
        return $stmt->fetchAll();
    }

    public static function addComment(int $postId, int $userId, string $comment, ?int $parentId = null): void {
        $stmt = self::connect()->prepare('INSERT INTO post_comments (post_id, user_id, parent_id, comment) VALUES (?, ?, ?, ?)');
        $stmt->execute([$postId, $userId, $parentId, $comment]);
    }
}

