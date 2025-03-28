<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/functions.php';

session_start();
require_login();

$db = (new Database())->connect();

// Get article
$articleId = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("
    SELECT * FROM articles 
    WHERE id = :id AND user_id = :user_id
");
$stmt->execute([
    ':id' => $articleId,
    ':user_id' => $_SESSION['user_id']
]);
$article = $stmt->fetch();

if (!$article) {
    set_flash_message('error', 'Article not found or you do not have permission to delete it');
    redirect('/articles/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // First delete all comments associated with the article
        $stmt = $db->prepare("DELETE FROM comments WHERE article_id = :article_id");
        $stmt->execute([':article_id' => $articleId]);

        // Then delete the article
        $stmt = $db->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->execute([':id' => $articleId]);

        set_flash_message('success', 'Article deleted successfully!');
        redirect('/articles/');
    } catch (PDOException $e) {
        error_log("Article deletion error: " . $e->getMessage());
        set_flash_message('error', 'Failed to delete article. Please try again.');
        redirect("/articles/show.php?id={$articleId}");
    }
}

ob_start();
include __DIR__.'/../templates/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Delete Article</h1>

    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-700 mb-6">Are you sure you want to delete the article titled "<strong><?= htmlspecialchars($article['title']) ?></strong>"? This action cannot be undone.</p>

        <form method="POST">
            <div class="flex justify-end">
                <a href="/articles/show.php?id=<?= $articleId ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800 mr-4">Cancel</a>
                <button type="submit" 
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Confirm Delete
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include __DIR__.'/../templates/footer.php';
ob_end_flush();
?>