<?php
require_once __DIR__.'/config/database.php';
require_once __DIR__.'/includes/functions.php';

session_start();
require_login();

$title = 'Dashboard - Nayash Blog';
$db = (new Database())->connect();

// Get user's articles
$stmt = $db->prepare("
    SELECT a.*, c.name as category 
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    WHERE a.user_id = :user_id
    ORDER BY a.created_at DESC
");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$articles = $stmt->fetchAll();

ob_start();
include __DIR__.'/templates/header.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Your Dashboard</h1>
        <a href="/articles/create.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>New Article
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Your Articles</h2>
        </div>
        
        <?php if (empty($articles)): ?>
            <div class="p-6 text-center text-gray-500">
                <p>You haven't written any articles yet.</p>
                <a href="/articles/create.php" class="text-blue-600 hover:underline mt-2 inline-block">Create your first article</a>
            </div>
        <?php else: ?>
            <div class="divide-y divide-gray-200">
                <?php foreach ($articles as $article): ?>
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-800">
                                    <a href="/articles/show.php?id=<?= $article['id'] ?>" class="hover:text-blue-600">
                                        <?= htmlspecialchars($article['title']) ?>
                                    </a>
                                </h3>
                                <div class="flex items-center mt-2">
                                    <span class="text-sm text-gray-500 mr-4">
                                        <?= date('M j, Y', strtotime($article['created_at'])) ?>
                                    </span>
                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                                        <?= htmlspecialchars($article['category']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="/articles/edit.php?id=<?= $article['id'] ?>" 
                                   class="text-blue-600 hover:text-blue-800 px-2 py-1 rounded hover:bg-blue-50">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/articles/delete.php?id=<?= $article['id'] ?>" 
                                   class="text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50"
                                   onclick="return confirm('Are you sure you want to delete this article?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include __DIR__.'/templates/footer.php';
ob_end_flush();
?>