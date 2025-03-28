<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/functions.php';

$title = 'Articles - Nayash Blog';
$db = (new Database())->connect();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Category filter
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : null;
$whereClause = $categoryFilter ? "WHERE a.category_id = :category_id" : "";
$params = $categoryFilter ? [':category_id' => $categoryFilter] : [];

// Get total count for pagination
$countStmt = $db->prepare("
    SELECT COUNT(*) as total 
    FROM articles a
    $whereClause
");
$countStmt->execute($params);
$totalArticles = $countStmt->fetch()['total'];
$totalPages = ceil($totalArticles / $perPage);

// Get articles
$stmt = $db->prepare("
    SELECT a.*, u.username, c.name as category 
    FROM articles a
    JOIN users u ON a.user_id = u.id
    JOIN categories c ON a.category_id = c.id
    $whereClause
    ORDER BY a.created_at DESC
    LIMIT :offset, :perPage
");
$params[':offset'] = $offset;
$params[':perPage'] = $perPage;
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Get all categories for filter
$stmt = $db->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

ob_start();
include __DIR__.'/../templates/header.php';
?>

<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">All Articles</h1>
        <?php if (is_logged_in()): ?>
            <a href="/articles/create.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>New Article
            </a>
        <?php endif; ?>
    </div>

    <?php if ($categoryFilter): ?>
        <div class="mb-4">
            <a href="/articles/" class="text-blue-600 hover:underline">
                ← Back to all articles
            </a>
        </div>
    <?php endif; ?>

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

    <?php if ($totalPages > 1): ?>
        <div class="mt-8 flex justify-center">
            <nav class="inline-flex rounded-md shadow">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?><?= $categoryFilter ? "&category=$categoryFilter" : '' ?>" 
                       class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
                        Previous
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?><?= $categoryFilter ? "&category=$categoryFilter" : '' ?>" 
                       class="<?= $i === $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?> 
                       px-3 py-2 border-t border-b">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page+1 ?><?= $categoryFilter ? "&category=$categoryFilter" : '' ?>" 
                       class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
                        Next
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php
include __DIR__.'/../templates/footer.php';
ob_end_flush();
?>