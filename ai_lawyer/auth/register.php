<?php
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка CSRF токена
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Недействительный токен безопасности';
    } else {
        $user = new User();
        $registerResult = $user->register(
            $_POST['email'],
            $_POST['password'],
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['company'] ?? '',
            $_POST['phone'] ?? ''
        );
        
        if ($registerResult['success']) {
            $success = $registerResult['message'];
            header('Location: ../dashboard/index.php');
            exit;
        } else {
            $error = $registerResult['message'];
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
    <title>Регистрация - AI Юрист</title>
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
                    Создать аккаунт
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
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="sr-only">Имя</label>
                            <input id="first_name" name="first_name" type="text" required 
                                   class="relative block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Имя" value="<?= Security::sanitizeOutput($_POST['first_name'] ?? '') ?>">
                        </div>
                        <div>
                            <label for="last_name" class="sr-only">Фамилия</label>
                            <input id="last_name" name="last_name" type="text" required 
                                   class="relative block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Фамилия" value="<?= Security::sanitizeOutput($_POST['last_name'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <input id="email" name="email" type="email" required 
                               class="relative block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Email адрес" value="<?= Security::sanitizeOutput($_POST['email'] ?? '') ?>">
                    </div>
                    
                    <div>
                        <label for="company" class="sr-only">Компания</label>
                        <input id="company" name="company" type="text" 
                               class="relative block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Компания (необязательно)" value="<?= Security::sanitizeOutput($_POST['company'] ?? '') ?>">
                    </div>
                    
                    <div>
                        <label for="phone" class="sr-only">Телефон</label>
                        <input id="phone" name="phone" type="tel" 
                               class="relative block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Телефон (необязательно)" value="<?= Security::sanitizeOutput($_POST['phone'] ?? '') ?>">
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
                    
                    <div class="text-xs text-gray-600">
                        <p>Пароль должен содержать:</p>
                        <ul class="list-disc list-inside mt-1 space-y-1">
                            <li>Минимум 8 символов</li>
                            <li>Заглавные и строчные буквы</li>
                            <li>Цифры и специальные символы</li>
                        </ul>
                    </div>
                </div>
                
                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus text-indigo-500 group-hover:text-indigo-400"></i>
                        </span>
                        Зарегистрироваться
                    </button>
                </div>
                
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Уже есть аккаунт? 
                        <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Войти
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
        
        // Валидация пароля в реальном времени
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const requirements = [
                { regex: /.{8,}/, text: 'Минимум 8 символов' },
                { regex: /[A-Z]/, text: 'Заглавные буквы' },
                { regex: /[a-z]/, text: 'Строчные буквы' },
                { regex: /[0-9]/, text: 'Цифры' },
                { regex: /[^A-Za-z0-9]/, text: 'Специальные символы' }
            ];
            
            // Можно добавить визуальную индикацию силы пароля
        });
    </script>
</body>
</html> 