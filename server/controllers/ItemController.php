<?php
/**
 * Item Controller
 *
 * Handles CRUD operations and search for lost/found items.
 * Business logic layer between API endpoints and the Item model.
 */

require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/Response.php';

class ItemController
{
    private ItemModel $itemModel;

    /** Allowed item categories */
    private const CATEGORIES = [
        'Electronics', 'Books', 'Clothing', 'Accessories',
        'Documents', 'Keys', 'Bags', 'Sports', 'Other',
    ];

    /** Allowed item types */
    private const TYPES = ['lost', 'found'];

    /** Allowed status values */
    private const STATUSES = ['active', 'resolved'];

    public function __construct(PDO $db)
    {
        $this->itemModel = new ItemModel($db);
    }

    /**
     * Create a new item listing.
     *
     * Expects multipart/form-data with optional image file.
     */
    public function create(): void
    {
        // Require authentication
        $this->requireAuth();

        $title       = Validator::sanitizeString($_POST['title'] ?? '');
        $description = Validator::sanitizeString($_POST['description'] ?? '');
        $category    = Validator::sanitizeString($_POST['category'] ?? 'Other');
        $type        = strtolower(trim($_POST['type'] ?? ''));
        $location    = Validator::sanitizeString($_POST['location'] ?? '');

        // --- Validation ---
        $errors = [];

        if (!Validator::isNotEmpty($title)) {
            $errors[] = 'Title is required.';
        } elseif (!Validator::maxLength($title, 150)) {
            $errors[] = 'Title must not exceed 150 characters.';
        }

        if (!Validator::isNotEmpty($description)) {
            $errors[] = 'Description is required.';
        }

        if (!Validator::isInList($category, self::CATEGORIES)) {
            $errors[] = 'Invalid category. Allowed: ' . implode(', ', self::CATEGORIES);
        }

        if (!Validator::isInList($type, self::TYPES)) {
            $errors[] = 'Type must be "lost" or "found".';
        }

        if (!Validator::isNotEmpty($location)) {
            $errors[] = 'Location is required.';
        } elseif (!Validator::maxLength($location, 200)) {
            $errors[] = 'Location must not exceed 200 characters.';
        }

        if (!empty($errors)) {
            Response::error(implode(' ', $errors), 422);
        }

        // --- Handle image upload ---
        $imagePath = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $validation = Validator::validateImageUpload($_FILES['image']);

            if (!$validation['valid']) {
                Response::error($validation['error'], 422);
            }

            $uploadDir = __DIR__ . '/../uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename  = uniqid('item_', true) . '.' . strtolower($extension);
            $targetPath = $uploadDir . $filename;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                Response::error('Failed to save uploaded image.', 500);
            }

            $imagePath = 'uploads/' . $filename;
        }

        // --- Create item ---
        try {
            $itemId = $this->itemModel->create([
                'user_id'     => $_SESSION['user_id'],
                'title'       => $title,
                'description' => $description,
                'category'    => $category,
                'type'        => $type,
                'location'    => $location,
                'image_path'  => $imagePath,
            ]);

            $item = $this->itemModel->findById($itemId);

            Response::success($item, 'Item created successfully.', 201);
        } catch (PDOException $e) {
            error_log('Item Create DB Error: ' . $e->getMessage());
            Response::error('Failed to create item. Please try again.', 500);
        }
    }

    /**
     * Fetch all items, optionally filtered by type.
     *
     * Query params: ?type=lost|found (optional)
     */
    public function readAll(): void
    {
        $type = null;

        if (isset($_GET['type'])) {
            $type = strtolower(trim($_GET['type']));
            if (!Validator::isInList($type, self::TYPES)) {
                Response::error('Invalid type filter. Use "lost" or "found".', 422);
            }
        }

        try {
            $items = $this->itemModel->findAll($type);
            Response::success($items, 'Items retrieved successfully.');
        } catch (PDOException $e) {
            error_log('Item Read DB Error: ' . $e->getMessage());
            Response::error('Failed to fetch items.', 500);
        }
    }

    /**
     * Get details of a single item by ID.
     *
     * Query params: ?id=X
     */
    public function readSingle(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id || !Validator::isPositiveInt($id)) {
            Response::error('A valid item ID is required.', 422);
        }

        try {
            $item = $this->itemModel->findById((int) $id);

            if (!$item) {
                Response::error('Item not found.', 404);
            }

            Response::success($item, 'Item retrieved successfully.');
        } catch (PDOException $e) {
            error_log('Item Single DB Error: ' . $e->getMessage());
            Response::error('Failed to fetch item.', 500);
        }
    }

    /**
     * Update an item's status.
     *
     * Expects JSON body: { id, status }
     * Only the item owner can update.
     */
    public function update(): void
    {
        $this->requireAuth();

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            Response::error('Invalid JSON input.', 400);
        }

        $id     = $input['id'] ?? null;
        $status = strtolower(trim($input['status'] ?? ''));

        if (!$id || !Validator::isPositiveInt($id)) {
            Response::error('A valid item ID is required.', 422);
        }

        if (!Validator::isInList($status, self::STATUSES)) {
            Response::error('Status must be "active" or "resolved".', 422);
        }

        try {
            // Verify ownership
            if (!$this->itemModel->isOwner((int) $id, (int) $_SESSION['user_id'])) {
                Response::error('You are not authorized to update this item.', 403);
            }

            $this->itemModel->updateStatus((int) $id, $status);
            $updatedItem = $this->itemModel->findById((int) $id);

            Response::success($updatedItem, 'Item updated successfully.');
        } catch (PDOException $e) {
            error_log('Item Update DB Error: ' . $e->getMessage());
            Response::error('Failed to update item.', 500);
        }
    }

    /**
     * Delete an item.
     *
     * Expects JSON body: { id }
     * Only the item owner can delete.
     */
    public function deleteItem(): void
    {
        $this->requireAuth();

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            Response::error('Invalid JSON input.', 400);
        }

        $id = $input['id'] ?? null;

        if (!$id || !Validator::isPositiveInt($id)) {
            Response::error('A valid item ID is required.', 422);
        }

        try {
            // Verify ownership
            if (!$this->itemModel->isOwner((int) $id, (int) $_SESSION['user_id'])) {
                Response::error('You are not authorized to delete this item.', 403);
            }

            // Get item to check for image before deletion
            $item = $this->itemModel->findById((int) $id);

            if (!$item) {
                Response::error('Item not found.', 404);
            }

            // Delete image file if it exists
            if (!empty($item['image_path'])) {
                $imagePath = __DIR__ . '/../' . $item['image_path'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $this->itemModel->delete((int) $id);

            Response::success(null, 'Item deleted successfully.');
        } catch (PDOException $e) {
            error_log('Item Delete DB Error: ' . $e->getMessage());
            Response::error('Failed to delete item.', 500);
        }
    }

    /**
     * Search items by keyword.
     *
     * Query params: ?q=keyword
     */
    public function search(): void
    {
        $keyword = Validator::sanitizeString($_GET['q'] ?? '');

        if (!Validator::isNotEmpty($keyword)) {
            Response::error('Search query "q" is required.', 422);
        }

        if (!Validator::minLength($keyword, 2)) {
            Response::error('Search query must be at least 2 characters.', 422);
        }

        try {
            $items = $this->itemModel->search($keyword);
            Response::success($items, count($items) . ' item(s) found.');
        } catch (PDOException $e) {
            error_log('Item Search DB Error: ' . $e->getMessage());
            Response::error('Search failed.', 500);
        }
    }

    /**
     * Helper: Require the user to be authenticated.
     */
    private function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            Response::error('Authentication required. Please log in.', 401);
        }
    }
}

// public function getLegacyItems() {}
