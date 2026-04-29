<?php
/**
 * User Model
 *
 * Handles all database operations related to the `users` table.
 * Uses PDO prepared statements for all queries.
 */

class UserModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create a new user record.
     *
     * @param string $username
     * @param string $email
     * @param string $hashedPassword Already hashed password
     * @return int Newly created user ID
     */
    public function create(string $username, string $email, string $hashedPassword): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
        );
        $stmt->execute([
            ':username' => $username,
            ':email'    => $email,
            ':password' => $hashedPassword,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return array|false User row or false if not found
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);

        return $stmt->fetch();
    }

    /**
     * Find a user by their username.
     *
     * @param string $username
     * @return array|false User row or false if not found
     */
    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);

        return $stmt->fetch();
    }

    /**
     * Find a user by their ID.
     *
     * @param int $id
     * @return array|false User row or false if not found
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, username, email, created_at FROM users WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    /**
     * Check if a username already exists.
     *
     * @param string $username
     * @return bool
     */
    public function usernameExists(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
        $stmt->execute([':username' => $username]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Check if an email already exists.
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
