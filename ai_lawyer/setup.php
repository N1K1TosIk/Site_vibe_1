<?php
// Файл установки системы AI-юриста
require_once __DIR__ . '/config/database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Подключение к MySQL без указания базы данных
        $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Чтение и выполнение SQL скрипта
        $sql = file_get_contents(__DIR__ . '/sql/setup.sql');
        
        // Выполнение всего скрипта одним запросом
        $pdo->exec($sql);
        
        $message = 'База данных успешно создана и настроена! Теперь вы можете использовать систему.';
        
    } catch (Exception $e) {
        $error = 'Ошибка при настройке базы данных: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка AI Юрист</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8">
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-blue-600">
                <i class="fas fa-gavel text-white text-2xl"></i>
            </div>
            <h1 class="mt-4 text-2xl font-bold text-gray-900">Установка AI Юрист</h1>
            <p class="mt-2 text-gray-600">Настройка базы данных системы</p>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-400 mr-2 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-green-700"><?= htmlspecialchars($message) ?></p>
                        <div class="mt-4">
                            <a href="index.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                <i class="fas fa-home mr-2"></i>Перейти на главную
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-400 mr-2 mt-0.5"></i>
                    <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$message): ?>
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Требования системы</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>PHP 7.4+ - <?= PHP_VERSION ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>MySQL 5.7+</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>PDO Extension</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>JSON Extension</span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Установка</h2>
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mr-2 mt-0.5"></i>
                        <div class="text-sm text-yellow-700">
                            <p class="font-medium">Убедитесь, что:</p>
                            <ul class="mt-2 list-disc list-inside space-y-1">
                                <li>MySQL сервер запущен</li>
                                <li>У пользователя root есть права на создание БД</li>
                                <li>Папка temp/ доступна для записи</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-download mr-2"></i>
                    Установить базу данных
                </button>
            </form>
        <?php endif; ?>

        <div class="mt-8 text-center text-sm text-gray-500">
            <p>AI Юрист v1.0</p>
            <p>Система создания и анализа юридических документов</p>
        </div>
    </div>
</body>
</html> 