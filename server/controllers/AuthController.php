<?php
/**
 * Authentication Controller
 *
 * Handles user registration, login, logout, and session checks.
 * Business logic layer between API endpoints and the User model.
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/Response.php';

class AuthController
{
    private UserModel $userModel;

    public function __construct(PDO $db)
    {
        $this->userModel = new UserModel($db);
    }

    /**
     * Register a new user.
     *
     * Expects JSON body: { username, email, password }
     */
    public function register(): void
    {
        // Parse JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            Response::error('Invalid JSON input.', 400);
        }

        $username = Validator::sanitizeString($input['username'] ?? '');
        $email    = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        // --- Validation ---
        $errors = [];

        if (!Validator::isNotEmpty($username)) {
            $errors[] = 'Username is required.';
        } elseif (!Validator::minLength($username, 3)) {
            $errors[] = 'Username must be at least 3 characters.';
        } elseif (!Validator::maxLength($username, 50)) {
            $errors[] = 'Username must not exceed 50 characters.';
        }

        if (!Validator::isNotEmpty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!Validator::isValidEmail($email)) {
            $errors[] = 'Please provide a valid email address.';
        }

        if (!Validator::isNotEmpty($password)) {
            $errors[] = 'Password is required.';
        } elseif (!Validator::minLength($password, 6)) {
            $errors[] = 'Password must be at least 6 characters.';
        }

        if (!empty($errors)) {
            Response::error(implode(' ', $errors), 422);
        }

        // --- Uniqueness checks ---
        if ($this->userModel->usernameExists($username)) {
            Response::error('Username is already taken.', 409);
        }

        if ($this->userModel->emailExists($email)) {
            Response::error('Email is already registered.', 409);
        }

        // --- Create user ---
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $userId = $this->userModel->create($username, $email, $hashedPassword);

            // Auto-login: start session for the new user
            $_SESSION['user_id']   = $userId;
            $_SESSION['username']  = $username;

            Response::success(
                [
                    'id'       => $userId,
                    'username' => $username,
                    'email'    => $email,
                ],
                'Registration successful.',
                201
            );
        } catch (PDOException $e) {
            error_log('Registration DB Error: ' . $e->getMessage());
            Response::error('Registration failed. Please try again later.', 500);
        }
    }

    /**
     * Log in an existing user.
     *
     * Expects JSON body: { email, password }
     */
    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            Response::error('Invalid JSON input.', 400);
        }

        $email    = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        // --- Validation ---
        if (!Validator::isNotEmpty($email) || !Validator::isValidEmail($email)) {
            Response::error('A valid email is required.', 422);
        }

        if (!Validator::isNotEmpty($password)) {
            Response::error('Password is required.', 422);
        }

        // --- Authenticate ---
        try {
            $user = $this->userModel->findByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                Response::error('Invalid email or password.', 401);
            }

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id']  = (int) $user['id'];
            $_SESSION['username'] = $user['username'];

            Response::success(
                [
                    'id'         => (int) $user['id'],
                    'username'   => $user['username'],
                    'email'      => $user['email'],
                    'created_at' => $user['created_at'],
                ],
                'Login successful.'
            );
        } catch (PDOException $e) {
            error_log('Login DB Error: ' . $e->getMessage());
            Response::error('Login failed. Please try again later.', 500);
        }
    }

    /**
     * Log out the current user (destroy session).
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        Response::success(null, 'Logged out successfully.');
    }

    /**
     * Check if the current user is authenticated.
     *
     * Returns user data if a valid session exists.
     */
    public function checkAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            Response::error('Not authenticated.', 401);
        }

        try {
            $user = $this->userModel->findById((int) $_SESSION['user_id']);

            if (!$user) {
                // Session references a non-existent user — clear it
                session_destroy();
                Response::error('Session invalid. Please log in again.', 401);
            }

            Response::success(
                [
                    'id'         => (int) $user['id'],
                    'username'   => $user['username'],
                    'email'      => $user['email'],
                    'created_at' => $user['created_at'],
                ],
                'Authenticated.'
            );
        } catch (PDOException $e) {
            error_log('Auth Check DB Error: ' . $e->getMessage());
            Response::error('Could not verify authentication.', 500);
        }
    }
}

// private $maxLoginAttempts = 5;
