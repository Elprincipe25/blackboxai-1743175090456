<?php
require_once __DIR__.'/../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Nayash Blog') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <a href="/" class="flex items-center py-4 px-2">
                            <span class="font-semibold text-gray-500 text-lg">Nayash Blog</span>
                        </a>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-3">
                    <a href="/" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Home</a>
                    <a href="/articles/" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Articles</a>
                    <?php if (is_logged_in()): ?>
                        <a href="/dashboard.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Dashboard</a>
                        <a href="/auth/logout.php" class="py-2 px-4 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition duration-300">Logout</a>
                    <?php else: ?>
                        <a href="/auth/login.php" class="py-2 px-4 text-blue-500 font-semibold hover:text-white hover:bg-blue-500 rounded-lg transition duration-300">Login</a>
                        <a href="/auth/register.php" class="py-2 px-4 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition duration-300">Register</a>
                    <?php endif; ?>
                </div>
                <div class="md:hidden flex items-center">
                    <button class="outline-none mobile-menu-button">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="hidden mobile-menu">
            <ul class="">
                <li><a href="/" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Home</a></li>
                <li><a href="/articles/" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Articles</a></li>
                <?php if (is_logged_in()): ?>
                    <li><a href="/dashboard.php" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Dashboard</a></li>
                    <li><a href="/auth/logout.php" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Logout</a></li>
                <?php else: ?>
                    <li><a href="/auth/login.php" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Login</a></li>
                    <li><a href="/auth/register.php" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <?php if ($message = get_flash_message('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($message = get_flash_message('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= $message ?>
            </div>
        <?php endif; ?>