<?php
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';

$user = new User();
$userData = null;
if (isset($_SESSION['user_id'])) {
    $userData = $user->getUserData($_SESSION['user_id']);
}
?>
<nav class="bg-white shadow-lg sticky top-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-12">
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <i class="fas fa-gavel text-blue-600 text-xl mr-2"></i>
                    <span class="text-lg font-bold text-gray-700">AI Юрист</span>
                </div>
                <div class="hidden md:ml-6 md:flex md:space-x-6">
                    <a href="../index.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-3 text-sm font-medium">Главная</a>
                    <a href="generator.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-3 text-sm font-medium">Генератор договоров</a>
                    <a href="analyzer.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-3 text-sm font-medium">Анализ документов</a>
                    <a href="documents.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-3 text-sm font-medium">Мои документы</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <?php if ($userData): ?>
                <div class="relative">
                    <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600" id="user-menu-button">
                        <div class="h-7 w-7 rounded-full bg-blue-600 flex items-center justify-center">
                            <span class="text-white text-xs font-medium">
                                <?= strtoupper(substr($userData['first_name'], 0, 1)) ?><?= strtoupper(substr($userData['last_name'], 0, 1)) ?>
                            </span>
                        </div>
                        <span class="ml-2 text-gray-700 text-sm"><?= Security::sanitizeOutput($userData['first_name']) ?></span>
                        <i class="fas fa-chevron-down ml-1 text-gray-400 text-xs"></i>
                    </button>
                    <div class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5" id="user-menu">
                        <div class="py-1">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-blue-700 hover:bg-gray-100 font-medium">
                                <i class="fas fa-user mr-2"></i>Профиль
                            </a>
                            <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>Настройки
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <a href="../auth/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>Выйти
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <a href="../auth/login.php" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Войти</a>
                <a href="../auth/register.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">Зарегистрироваться</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<script>
// Меню пользователя
if (document.getElementById('user-menu-button')) {
    document.getElementById('user-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('user-menu');
        menu.classList.toggle('hidden');
    });
    document.addEventListener('click', function(event) {
        const button = document.getElementById('user-menu-button');
        const menu = document.getElementById('user-menu');
        if (button && menu && !button.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
}
</script> 