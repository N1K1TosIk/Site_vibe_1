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

// Обработка сохранения настроек
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Недействительный токен безопасности';
    } else {
        // В реальном проекте здесь были бы настройки пользователя
        $success = 'Настройки сохранены';
    }
}

$csrfToken = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки - AI Юрист</title>
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
                                <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Профиль
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-blue-700 hover:bg-gray-100 font-medium">
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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Настройки</h1>
            <p class="mt-2 text-gray-600">Персонализируйте ваш опыт работы с AI Юристом</p>
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

        <form method="POST" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            
            <!-- Язык и регион -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Язык и регион</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700">Язык интерфейса</label>
                        <select id="language" name="language" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="ru" selected>Русский</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700">Часовой пояс</label>
                        <select id="timezone" name="timezone" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="Europe/Moscow" selected>Москва (UTC+3)</option>
                            <option value="Europe/Samara">Самара (UTC+4)</option>
                            <option value="Asia/Yekaterinburg">Екатеринбург (UTC+5)</option>
                            <option value="Asia/Novosibirsk">Новосибирск (UTC+7)</option>
                            <option value="Asia/Vladivostok">Владивосток (UTC+10)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Уведомления -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Уведомления</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Email уведомления</h3>
                            <p class="text-sm text-gray-500">Получать уведомления о важных событиях на email</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Уведомления о безопасности</h3>
                            <p class="text-sm text-gray-500">Уведомления о входах и изменениях в аккаунте</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="security_notifications" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Маркетинговые уведомления</h3>
                            <p class="text-sm text-gray-500">Информация о новых функциях и обновлениях</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="marketing_notifications" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Приватность -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Приватность и безопасность</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Двухфакторная аутентификация</h3>
                            <p class="text-sm text-gray-500">Дополнительный уровень защиты аккаунта</p>
                        </div>
                        <button type="button" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            Настроить
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Автоматический выход</h3>
                            <p class="text-sm text-gray-500">Выход из системы при неактивности</p>
                        </div>
                        <select name="auto_logout" class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="30">30 минут</option>
                            <option value="60" selected>1 час</option>
                            <option value="480">8 часов</option>
                            <option value="1440">24 часа</option>
                            <option value="0">Никогда</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Показывать активность</h3>
                            <p class="text-sm text-gray-500">Отображать статус "онлайн" другим пользователям</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="show_activity" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Данные и экспорт -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Данные аккаунта</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Экспорт данных</h3>
                            <p class="text-sm text-gray-500">Скачать копию всех ваших данных</p>
                        </div>
                        <button type="button" class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                            <i class="fas fa-download mr-2"></i>Экспорт
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-red-900">Удаление аккаунта</h3>
                            <p class="text-sm text-red-500">Навсегда удалить аккаунт и все данные</p>
                        </div>
                        <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i>Удалить
                        </button>
                    </div>
                </div>
            </div>

            <!-- Кнопки сохранения -->
            <div class="flex justify-end space-x-4">
                <a href="profile.php" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50">
                    Отмена
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>Сохранить настройки
                </button>
            </div>
        </form>
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
        
        function confirmDelete() {
            if (confirm('Вы уверены, что хотите удалить аккаунт? Это действие нельзя отменить.')) {
                if (confirm('Все ваши документы и данные будут безвозвратно утеряны. Продолжить?')) {
                    alert('Функция удаления аккаунта будет доступна в следующих версиях.');
                }
            }
        }
    </script>
</body>
</html> 