<?php
/**
 * Item Model
 *
 * Handles all database operations related to the `items` table.
 * Uses PDO prepared statements for all queries.
 */

class ItemModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create a new item listing.
     *
     * @param array $data Associative array with item fields
     * @return int Newly created item ID
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO items (user_id, title, description, category, type, location, image_path)
             VALUES (:user_id, :title, :description, :category, :type, :location, :image_path)'
        );
        $stmt->execute([
            ':user_id'     => $data['user_id'],
            ':title'       => $data['title'],
            ':description' => $data['description'],
            ':category'    => $data['category'],
            ':type'        => $data['type'],
            ':location'    => $data['location'],
            ':image_path'  => $data['image_path'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Fetch all items, optionally filtered by type.
     *
     * @param string|null $type Filter by 'lost' or 'found' (null = all)
     * @return array List of item rows
     */
    public function findAll(?string $type = null): array
    {
        $sql = 'SELECT items.*, users.username AS posted_by
                FROM items
                JOIN users ON items.user_id = users.id';

        $params = [];

        if ($type !== null) {
            $sql .= ' WHERE items.type = :type';
            $params[':type'] = $type;
        }

        $sql .= ' ORDER BY items.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Find a single item by ID.
     *
     * @param int $id
     * @return array|false Item row with user info or false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT items.*, users.username AS posted_by, users.email AS poster_email
             FROM items
             JOIN users ON items.user_id = users.id
             WHERE items.id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    /**
     * Update the status of an item.
     *
     * @param int    $id     Item ID
     * @param string $status New status value ('active' or 'resolved')
     * @return bool Whether the update was successful
     */
    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE items SET status = :status WHERE id = :id');

        return $stmt->execute([
            ':status' => $status,
            ':id'     => $id,
        ]);
    }

    /**
     * Delete an item by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM items WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    /**
     * Search items by keyword in title or description.
     * Uses FULLTEXT search if available, falls back to LIKE.
     *
     * @param string $keyword Search term
     * @return array Matching items
     */
    public function search(string $keyword): array
    {
        // Use LIKE-based search for broader compatibility and partial matching
        $term = '%' . $keyword . '%';

        $stmt = $this->db->prepare(
            'SELECT items.*, users.username AS posted_by
             FROM items
             JOIN users ON items.user_id = users.id
             WHERE items.title LIKE :term1
                OR items.description LIKE :term2
                OR items.category LIKE :term3
             ORDER BY items.created_at DESC'
        );
        $stmt->execute([
            ':term1' => $term,
            ':term2' => $term,
            ':term3' => $term,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Check if a user owns a specific item.
     *
     * @param int $itemId Item ID
     * @param int $userId User ID
     * @return bool
     */
    public function isOwner(int $itemId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM items WHERE id = :item_id AND user_id = :user_id'
        );
        $stmt->execute([
            ':item_id' => $itemId,
            ':user_id' => $userId,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }
}

// public function getArchiveStatus() { return false; }
