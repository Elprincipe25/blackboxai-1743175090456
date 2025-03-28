<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/functions.php';

session_start();
require_login();

$title = 'Create Article - Nayash Blog';
$db = (new Database())->connect();

$errors = [];
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $content = sanitize($_POST['content'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);

    // Validate inputs
    if (empty($title)) {
        $errors['title'] = 'Title is required';
    } elseif (strlen($title) < 5) {
        $errors['title'] = 'Title must be at least 5 characters';
    }

    if (empty($content)) {
        $errors['content'] = 'Content is required';
    } elseif (strlen($content) < 50) {
        $errors['content'] = 'Content must be at least 50 characters';
    }

    if ($category_id <= 0) {
        $errors['category_id'] = 'Please select a category';
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                INSERT INTO articles (user_id, category_id, title, content)
                VALUES (:user_id, :category_id, :title, :content)
            ");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':category_id' => $category_id,
                ':title' => $title,
                ':content' => $content
            ]);

            set_flash_message('success', 'Article created successfully!');
            redirect('/articles/');
        } catch (PDOException $e) {
            error_log("Article creation error: " . $e->getMessage());
            $errors['general'] = 'Failed to create article. Please try again.';
        }
    }
}

ob_start();
include __DIR__.'/../templates/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Create New Article</h1>

    <?php if (isset($errors['general'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= $errors['general'] ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
            <input type="text" id="title" name="title" 
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            <?php if (isset($errors['title'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= $errors['title'] ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-6">
            <label for="category_id" class="block text-gray-700 font-medium mb-2">Category</label>
            <select id="category_id" name="category_id" 
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" 
                        <?= (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category_id'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= $errors['category_id'] ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-6">
            <label for="content" class="block text-gray-700 font-medium mb-2">Content</label>
            <textarea id="content" name="content" rows="10"
                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            <?php if (isset($errors['content'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= $errors['content'] ?></p>
            <?php endif; ?>
        </div>

        <div class="flex justify-end">
            <a href="/articles/" class="px-4 py-2 text-gray-600 hover:text-gray-800 mr-4">Cancel</a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Publish Article
            </button>
        </div>
    </form>
</div>

<?php
include __DIR__.'/../templates/footer.php';
ob_end_flush();
?>