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
$token = $_GET['token'] ?? '';

// Проверка токена
if (empty($token)) {
    $error = 'Недействительная ссылка восстановления';
} else {
    $tokenValidation = $user->validateResetToken($token);
    if (!$tokenValidation['valid']) {
        $error = $tokenValidation['message'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Недействительный токен безопасности';
    } else {
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($newPassword) || empty($confirmPassword)) {
            $error = 'Заполните все поля';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Пароли не совпадают';
        } else {
            $result = $user->resetPassword($token, $newPassword);
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
    <title>Новый пароль - AI Юрист</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-indigo-600">
                    <i class="fas fa-lock text-white text-2xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Новый пароль
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Создайте новый безопасный пароль для вашего аккаунта
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mr-2 mt-0.5"></i>
                        <div>
                            <p class="text-sm text-red-700"><?= Security::sanitizeOutput($error) ?></p>
                            <div class="mt-4">
                                <a href="forgot-password.php" class="text-sm font-medium text-red-600 hover:text-red-500">
                                    Запросить новую ссылку →
                                </a>
                            </div>
                        </div>
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
                                <a href="login.php" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Войти в систему
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$error && !$success): ?>
                <form class="mt-8 space-y-6" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="space-y-4">
                        <div class="relative">
                            <label for="new_password" class="sr-only">Новый пароль</label>
                            <input id="new_password" name="new_password" type="password" required 
                                   class="relative block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Новый пароль">
                            <button type="button" onclick="togglePassword('new_password')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye text-gray-400" id="new_password-toggle"></i>
                            </button>
                        </div>
                        
                        <div class="relative">
                            <label for="confirm_password" class="sr-only">Подтвердите пароль</label>
                            <input id="confirm_password" name="confirm_password" type="password" required 
                                   class="relative block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Подтвердите пароль">
                            <button type="button" onclick="togglePassword('confirm_password')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye text-gray-400" id="confirm_password-toggle"></i>
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
                                <i class="fas fa-save text-indigo-500 group-hover:text-indigo-400"></i>
                            </span>
                            Установить новый пароль
                        </button>
                    </div>
                </form>
            <?php endif; ?>
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
        document.getElementById('new_password').addEventListener('input', function(e) {
            const password = e.target.value;
            const requirements = [
                { regex: /.{8,}/, text: 'Минимум 8 символов' },
                { regex: /[A-Z]/, text: 'Заглавные буквы' },
                { regex: /[a-z]/, text: 'Строчные буквы' },
                { regex: /[0-9]/, text: 'Цифры' },
                { regex: /[^A-Za-z0-9]/, text: 'Специальные символы' }
            ];
            
            // Визуальная индикация можно добавить позже
        });

        // Проверка совпадения паролей
        document.getElementById('confirm_password').addEventListener('input', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = e.target.value;
            
            if (confirmPassword && newPassword !== confirmPassword) {
                e.target.style.borderColor = '#ef4444';
            } else {
                e.target.style.borderColor = '#d1d5db';
            }
        });
    </script>
</body>
</html> 