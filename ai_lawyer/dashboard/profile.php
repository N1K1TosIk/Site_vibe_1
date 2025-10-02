<?php
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';

// Проверка авторизации
$user = new User();
if (!$user->checkSession()) {
    header('Location: ../auth/login.php');
    exit;
}

// Только после проверки авторизации можно регенерировать ID
Security::configureSession();

$userData = $user->getUserData($_SESSION['user_id']);
if (!$userData) {
    header('Location: ../auth/login.php');
    exit;
}

$error = '';
$success = '';

// Обработка обновления профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Недействительный токен безопасности';
    } else {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'update_profile':
                    $result = $user->updateProfile(
                        $_SESSION['user_id'],
                        $_POST['first_name'],
                        $_POST['last_name'],
                        $_POST['email'],
                        $_POST['company'] ?? '',
                        $_POST['phone'] ?? ''
                    );
                    
                    if ($result['success']) {
                        $success = $result['message'];
                        $userData = $user->getUserData($_SESSION['user_id']); // Обновляем данные
                    } else {
                        $error = $result['message'];
                    }
                    break;
                    
                case 'change_password':
                    $result = $user->changePassword(
                        $_SESSION['user_id'],
                        $_POST['current_password'],
                        $_POST['new_password'],
                        $_POST['confirm_password']
                    );
                    
                    if ($result['success']) {
                        $success = $result['message'];
                    } else {
                        $error = $result['message'];
                    }
                    break;
                    
                case 'logout_all_devices':
                    $result = $user->logoutAllDevices($_SESSION['user_id']);
                    if ($result['success']) {
                        $success = $result['message'];
                    } else {
                        $error = $result['message'];
                    }
                    break;
            }
        }
    }
}

// Получение статистики пользователя
$userStats = $user->getUserStats($_SESSION['user_id']);
$activeSessions = $user->getActiveSessions($_SESSION['user_id']);
$recentActions = $user->getRecentActions($_SESSION['user_id'], 10);

$csrfToken = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - AI Юрист</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen pt-12">
    <!-- Навигация -->
    <nav class="bg-white shadow-lg sticky top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-gavel text-blue-600 text-2xl mr-2"></i>
                        <span class="text-xl font-bold text-gray-700">AI Юрист</span>
                    </div>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="index.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-4 text-sm font-medium">
                            Главная
                        </a>
                        <a href="generator.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-4 text-sm font-medium">
                            Генератор договоров
                        </a>
                        <a href="analyzer.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-4 text-sm font-medium">
                            Анализ документов
                        </a>
                        <a href="documents.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-4 text-sm font-medium">
                            Мои документы
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600" id="user-menu-button">
                            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    <?= strtoupper(substr($userData['first_name'], 0, 1)) ?><?= strtoupper(substr($userData['last_name'], 0, 1)) ?>
                                </span>
                            </div>
                            <span class="ml-2 text-gray-700"><?= Security::sanitizeOutput($userData['first_name']) ?></span>
                            <i class="fas fa-chevron-down ml-1 text-gray-400"></i>
                        </button>
                        <div class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5" id="user-menu">
                            <div class="py-1">
                                <a href="#" class="block px-4 py-2 text-sm text-blue-700 hover:bg-gray-100 font-medium">
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
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Личный кабинет</h1>
            <p class="mt-2 text-gray-600">Управление профилем и настройками аккаунта</p>
        </div>

        <!-- Уведомления -->
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-400 mr-2 mt-0.5"></i>
                    <p class="text-sm text-red-700"><?= Security::sanitizeOutput($error) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-400 mr-2 mt-0.5"></i>
                    <p class="text-sm text-green-700"><?= Security::sanitizeOutput($success) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Профиль пользователя -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Основная информация -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Основная информация</h2>
                    </div>
                    <form method="POST" class="p-6">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">Имя</label>
                                <input type="text" name="first_name" id="first_name" required
                                       value="<?= Security::sanitizeOutput($userData['first_name']) ?>"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Фамилия</label>
                                <input type="text" name="last_name" id="last_name" required
                                       value="<?= Security::sanitizeOutput($userData['last_name']) ?>"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" required
                                       value="<?= Security::sanitizeOutput($userData['email']) ?>"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700">Компания</label>
                                <input type="text" name="company" id="company"
                                       value="<?= Security::sanitizeOutput($userData['company']) ?>"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Телефон</label>
                                <input type="tel" name="phone" id="phone"
                                       value="<?= Security::sanitizeOutput($userData['phone']) ?>"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i>Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Смена пароля -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Смена пароля</h2>
                    </div>
                    <form method="POST" class="p-6">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="space-y-6">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Текущий пароль</label>
                                <input type="password" name="current_password" id="current_password" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700">Новый пароль</label>
                                <input type="password" name="new_password" id="new_password" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Минимум 8 символов, включая заглавные и строчные буквы, цифры и спецсимволы</p>
                            </div>
                            
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Подтвердите пароль</label>
                                <input type="password" name="confirm_password" id="confirm_password" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-key mr-2"></i>Изменить пароль
                            </button>
                        </div>
                    </form>
                </div>

                <!-- История действий -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">История действий</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($recentActions)): ?>
                            <p class="text-gray-500 text-center py-4">История действий пуста</p>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($recentActions as $action): ?>
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <?php
                                                $iconMap = [
                                                    'user_login' => 'fa-sign-in-alt',
                                                    'user_logout' => 'fa-sign-out-alt',
                                                    'user_registered' => 'fa-user-plus',
                                                    'contract_generated' => 'fa-file-contract',
                                                    'document_analyzed' => 'fa-search',
                                                    'profile_updated' => 'fa-user-edit',
                                                    'password_changed' => 'fa-key'
                                                ];
                                                $icon = $iconMap[$action['action']] ?? 'fa-info-circle';
                                                ?>
                                                <i class="fas <?= $icon ?> text-blue-600 text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-900">
                                                <?php
                                                $actionNames = [
                                                    'user_login' => 'Вход в систему',
                                                    'user_logout' => 'Выход из системы',
                                                    'user_registered' => 'Регистрация',
                                                    'contract_generated' => 'Создание договора',
                                                    'document_analyzed' => 'Анализ документа',
                                                    'profile_updated' => 'Обновление профиля',
                                                    'password_changed' => 'Смена пароля'
                                                ];
                                                echo $actionNames[$action['action']] ?? $action['action'];
                                                ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <?= date('d.m.Y H:i', strtotime($action['created_at'])) ?>
                                                <?php if ($action['ip_address']): ?>
                                                    · IP: <?= Security::sanitizeOutput($action['ip_address']) ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Боковая панель -->
            <div class="space-y-8">
                <!-- Аватар -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Аватар</h2>
                    </div>
                    <div class="p-6 text-center">
                        <div class="mx-auto h-24 w-24 rounded-full bg-blue-600 flex items-center justify-center mb-4">
                            <span class="text-white text-2xl font-medium">
                                <?= strtoupper(substr($userData['first_name'], 0, 1)) ?><?= strtoupper(substr($userData['last_name'], 0, 1)) ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">
                            <?= Security::sanitizeOutput($userData['first_name']) ?> <?= Security::sanitizeOutput($userData['last_name']) ?>
                        </p>
                        <p class="text-xs text-gray-400">
                            Используется аватар по умолчанию
                        </p>
                    </div>
                </div>

                <!-- Статистика -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Статистика</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600"><?= $userStats['total_documents'] ?? 0 ?></div>
                                <div class="text-sm text-gray-500">Всего документов</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600"><?= $userStats['generated_contracts'] ?? 0 ?></div>
                                <div class="text-sm text-gray-500">Создано договоров</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600"><?= $userStats['analyzed_documents'] ?? 0 ?></div>
                                <div class="text-sm text-gray-500">Проанализировано</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Безопасность -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Безопасность</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Активные сессии</h3>
                                <p class="text-sm text-gray-500"><?= count($activeSessions) ?> активных устройств</p>
                            </div>
                            
                            <div>
                                <p class="text-xs text-gray-500 mb-3">Последний вход:</p>
                                <p class="text-sm text-gray-700">
                                    <?= $userData['last_login'] ? date('d.m.Y H:i', strtotime($userData['last_login'])) : 'Никогда' ?>
                                </p>
                            </div>
                            
                            <form method="POST" class="mt-4">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <input type="hidden" name="action" value="logout_all_devices">
                                <button type="submit" onclick="return confirm('Вы уверены? Все активные сессии будут закрыты.')"
                                        class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-shield-alt mr-2"></i>Выйти со всех устройств
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Тариф -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Тарифный план</h2>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-900 mb-2">Бесплатный</div>
                            <p class="text-sm text-gray-500 mb-4">Базовые возможности</p>
                            <a href="pricing.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                <i class="fas fa-crown mr-2"></i>Обновить тариф
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Меню пользователя
        document.getElementById('user-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        });

        // Закрытие меню при клике вне его
        document.addEventListener('click', function(event) {
            const button = document.getElementById('user-menu-button');
            const menu = document.getElementById('user-menu');
            
            if (!button.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Валидация формы смены пароля
        document.querySelector('form[action="change_password"] button[type="submit"]').addEventListener('click', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Пароли не совпадают');
                return;
            }
            
            if (newPassword.length < 8) {
                e.preventDefault();
                alert('Пароль должен содержать минимум 8 символов');
                return;
            }
        });
    </script>
</body>
</html> 