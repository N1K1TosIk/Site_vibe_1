<?php
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';

// Проверка уже авторизованного пользователя
$user = new User();
if ($user->checkSession()) {
    header('Location: ../dashboard/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Недействительный токен безопасности';
    } else {
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            $error = 'Введите email адрес';
        } elseif (!Security::validateEmail($email)) {
            $error = 'Некорректный email адрес';
            } else {
            $result = $user->initiatePasswordReset($email);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Генерируем CSRF токен ДО регенерации ID сессии
$csrfToken = Security::generateCSRFToken();

// Теперь можно безопасно регенерировать ID
Security::configureSession();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля - AI Юрист</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-indigo-600">
                    <i class="fas fa-key text-white text-2xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Восстановление пароля
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Введите ваш email для получения ссылки восстановления
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mr-2 mt-0.5"></i>
                        <p class="text-sm text-red-700"><?= Security::sanitizeOutput($error) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <i class="fas fa-check-circle text-green-400 mr-2 mt-0.5"></i>
                        <div>
                            <p class="text-sm text-green-700"><?= Security::sanitizeOutput($success) ?></p>
                            <div class="mt-4">
                                <a href="login.php" class="text-sm font-medium text-green-600 hover:text-green-500">
                                    ← Вернуться к входу
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form class="mt-8 space-y-6" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <input id="email" name="email" type="email" required 
                               class="relative block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Email адрес" value="<?= Security::sanitizeOutput($_POST['email'] ?? '') ?>">
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-paper-plane text-indigo-500 group-hover:text-indigo-400"></i>
                            </span>
                            Отправить ссылку восстановления
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Вспомнили пароль? 
                            <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                                Войти
                            </a>
                        </p>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Информация о безопасности -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Безопасность
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Ссылка действительна в течение 1 часа</li>
                                <li>После использования ссылка становится недействительной</li>
                                <li>Если письмо не пришло, проверьте папку "Спам"</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 