</main>
        
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-6xl mx-auto px-4 py-8">
                <div class="md:flex md:justify-between">
                    <div class="mb-8 md:mb-0">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Nayash Blog</h3>
                        <p class="text-gray-500">Sharing knowledge and insights on technology, business, and more.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-8 sm:grid-cols-3">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Navigation</h3>
                            <ul class="space-y-2">
                                <li><a href="/" class="text-gray-500 hover:text-blue-500">Home</a></li>
                                <li><a href="/articles/" class="text-gray-500 hover:text-blue-500">Articles</a></li>
                                <li><a href="/about.php" class="text-gray-500 hover:text-blue-500">About</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Account</h3>
                            <ul class="space-y-2">
                                <?php if (is_logged_in()): ?>
                                    <li><a href="/dashboard.php" class="text-gray-500 hover:text-blue-500">Dashboard</a></li>
                                    <li><a href="/auth/logout.php" class="text-gray-500 hover:text-blue-500">Logout</a></li>
                                <?php else: ?>
                                    <li><a href="/auth/login.php" class="text-gray-500 hover:text-blue-500">Login</a></li>
                                    <li><a href="/auth/register.php" class="text-gray-500 hover:text-blue-500">Register</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Legal</h3>
                            <ul class="space-y-2">
                                <li><a href="/privacy.php" class="text-gray-500 hover:text-blue-500">Privacy Policy</a></li>
                                <li><a href="/terms.php" class="text-gray-500 hover:text-blue-500">Terms of Service</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-200 text-center">
                    <p class="text-gray-500 text-sm">&copy; <?= date('Y') ?> Nayash Blog. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <script>
            // Mobile menu toggle
            const btn = document.querySelector('.mobile-menu-button');
            const menu = document.querySelector('.mobile-menu');

            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        </script>
    </body>
</html>