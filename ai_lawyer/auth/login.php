<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';

// Генерируем CSRF токен ДО регенерации ID сессии
$csrfToken = Security::generateCSRFToken();

// Теперь можно безопасно регенерировать ID
Security::configureSession();

// Проверка уже авторизованного пользователя
$user = new User();
if ($user->checkSession()) {
    header('Location: ../dashboard/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка CSRF токена
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Недействительный токен безопасности';
    } else {
        $loginResult = $user->login(
            $_POST['email'],
            $_POST['password'],
            isset($_POST['remember_me'])
        );
        
        if ($loginResult['success']) {
            header('Location: ../dashboard/index.php');
            exit;
        } else {
            $error = $loginResult['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - AI Юрист</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-indigo-600">
                    <i class="fas fa-gavel text-white text-2xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Вход в систему
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    AI-помощник для юридических документов
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
                        <p class="text-sm text-green-700"><?= Security::sanitizeOutput($success) ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <form class="mt-8 space-y-6" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                
                <div class="space-y-4">
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <input id="email" name="email" type="email" required 
                               class="relative block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Email адрес" value="<?= Security::sanitizeOutput($_POST['email'] ?? '') ?>">
                    </div>
                    
                    <div class="relative">
                        <label for="password" class="sr-only">Пароль</label>
                        <input id="password" name="password" type="password" required 
                               class="relative block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Пароль">
                        <button type="button" onclick="togglePassword('password')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400" id="password-toggle"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox" 
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                            Запомнить меня
                        </label>
                    </div>
                    
                    <div class="text-sm">
                        <a href="forgot-password.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Забыли пароль?
                        </a>
                    </div>
                </div>
                
                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-indigo-500 group-hover:text-indigo-400"></i>
                        </span>
                        Войти
                    </button>
                </div>
                
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Нет аккаунта? 
                        <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Зарегистрироваться
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggle = document.getElementById(fieldId + '-toggle');
            
            if (field.type === 'password') {
                field.type = 'text';
                toggle.className = 'fas fa-eye-slash text-gray-400';
            } else {
                field.type = 'password';
                toggle.className = 'fas fa-eye text-gray-400';
            }
        }
    </script>
</body>
</html>
<?php exit; ?> 