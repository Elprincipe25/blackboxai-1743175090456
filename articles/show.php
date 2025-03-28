<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/functions.php';

$db = (new Database())->connect();

// Get article
$articleId = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("
    SELECT a.*, u.username, c.name as category 
    FROM articles a
    JOIN users u ON a.user_id = u.id
    JOIN categories c ON a.category_id = c.id
    WHERE a.id = :id
");
$stmt->execute([':id' => $articleId]);
$article = $stmt->fetch();

if (!$article) {
    set_flash_message('error', 'Article not found');
    redirect('/articles/');
}

$title = htmlspecialchars($article['title']) . ' - Nayash Blog';

// Get comments
$stmt = $db->prepare("
    SELECT c.*, u.username 
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.article_id = :article_id
    ORDER BY c.created_at DESC
");
$stmt->execute([':article_id' => $articleId]);
$comments = $stmt->fetchAll();

// Handle comment submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    $content = sanitize($_POST['content'] ?? '');

    if (empty($content)) {
        $errors['content'] = 'Comment cannot be empty';
    } elseif (strlen($content) < 5) {
        $errors['content'] = 'Comment must be at least 5 characters';
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                INSERT INTO comments (article_id, user_id, content)
                VALUES (:article_id, :user_id, :content)
            ");
            $stmt->execute([
                ':article_id' => $articleId,
                ':user_id' => $_SESSION['user_id'],
                ':content' => $content
            ]);

            set_flash_message('success', 'Comment added successfully!');
            redirect("/articles/show.php?id={$articleId}");
        } catch (PDOException $e) {
            error_log("Comment creation error: " . $e->getMessage());
            $errors['general'] = 'Failed to add comment. Please try again.';
        }
    }
}

ob_start();
include __DIR__.'/../templates/header.php';
?>

<div class="max-w-4xl mx-auto">
    <article class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full uppercase font-semibold tracking-wide">
                    <?= htmlspecialchars($article['category']) ?>
                </span>
                <span class="text-gray-500 text-sm ml-2">
                    <?= date('M j, Y', strtotime($article['created_at'])) ?>
                </span>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($article['title']) ?></h1>
            <div class="flex items-center mb-6">
                <span class="text-gray-600">By <?= htmlspecialchars($article['username']) ?></span>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $article['user_id']): ?>
                    <div class="ml-auto">
                        <a href="/articles/edit.php?id=<?= $article['id'] ?>" class="text-blue-600 hover:text-blue-800 mr-4">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="/articles/delete.php?id=<?= $article['id'] ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this article?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="prose max-w-none text-gray-700">
                <?= nl2br(htmlspecialchars($article['content'])) ?>
            </div>
        </div>
    </article>

    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Comments (<?= count($comments) ?>)</h2>
        
        <?php if (is_logged_in()): ?>
            <form method="POST" class="mb-8">
                <div class="mb-4">
                    <label for="content" class="block text-gray-700 font-medium mb-2">Add a comment</label>
                    <textarea id="content" name="content" rows="3"
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Share your thoughts..."></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['content'] ?></p>
                    <?php endif; ?>
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Post Comment
                </button>
            </form>
        <?php else: ?>
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-6">
                <p>Please <a href="/auth/login.php" class="text-blue-600 hover:underline">login</a> to post a comment.</p>
            </div>
        <?php endif; ?>

        <div class="space-y-6">
            <?php if (empty($comments)): ?>
                <p class="text-gray-500">No comments yet. Be the first to comment!</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                        <div class="flex items-center mb-2">
                            <span class="font-medium text-gray-800"><?= htmlspecialchars($comment['username']) ?></span>
                            <span class="text-gray-500 text-sm ml-2">
                                <?= date('M j, Y \a\t g:i a', strtotime($comment['created_at'])) ?>
                            </span>
                        </div>
                        <p class="text-gray-700"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include __DIR__.'/../templates/footer.php';
ob_end_flush();
?>