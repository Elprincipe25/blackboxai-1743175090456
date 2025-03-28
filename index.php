<?php
require_once __DIR__.'/config/database.php';
require_once __DIR__.'/includes/functions.php';

$title = 'Home - Nayash Blog';
$db = (new Database())->connect();

// Get featured articles
$stmt = $db->query("
    SELECT a.*, u.username, c.name as category 
    FROM articles a
    JOIN users u ON a.user_id = u.id
    JOIN categories c ON a.category_id = c.id
    ORDER BY a.created_at DESC
    LIMIT 6
");
$articles = $stmt->fetchAll();

// Get all categories for filter
$stmt = $db->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

ob_start();
include __DIR__.'/templates/header.php';
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Latest Articles</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($articles as $article): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-2">
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full uppercase font-semibold tracking-wide">
                            <?= htmlspecialchars($article['category']) ?>
                        </span>
                        <span class="text-gray-500 text-sm ml-2">
                            <?= date('M j, Y', strtotime($article['created_at'])) ?>
                        </span>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">
                        <a href="/articles/show.php?id=<?= $article['id'] ?>" class="hover:text-blue-600">
                            <?= htmlspecialchars($article['title']) ?>
                        </a>
                    </h2>
                    <p class="text-gray-600 mb-4">
                        <?= substr(htmlspecialchars($article['content']), 0, 150) ?>...
                    </p>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-500">By <?= htmlspecialchars($article['username']) ?></span>
                        <a href="/articles/show.php?id=<?= $article['id'] ?>" class="ml-auto text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Read more →
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Browse by Category</h2>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($categories as $category): ?>
            <a href="/articles/?category=<?= $category['id'] ?>" 
               class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full hover:bg-blue-100 hover:text-blue-800 transition-colors duration-300">
                <?= htmlspecialchars($category['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php
include __DIR__.'/templates/footer.php';
ob_end_flush();
?>