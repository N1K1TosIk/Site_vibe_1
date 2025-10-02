<?php
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';

// Проверка авторизации (опционально)
$user = new User();
$isLoggedIn = $user->checkSession();
$userData = null;

if ($isLoggedIn) {
    $userData = $user->getUserData($_SESSION['user_id']);
}

// Только после проверки авторизации можно регенерировать ID
Security::configureSession();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О проекте - AI Юрист</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 pt-12">
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
                        <a href="<?= $isLoggedIn ? 'index.php' : '../index.php' ?>" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-4 text-sm font-medium">
                            Главная
                        </a>
                        <?php if ($isLoggedIn): ?>
                            <a href="generator.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-4 text-sm font-medium">
                                Генератор договоров
                            </a>
                            <a href="analyzer.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-4 text-sm font-medium">
                                Анализ документов
                            </a>
                            <a href="documents.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-4 text-sm font-medium">
                                Мои документы
                            </a>
                        <?php endif; ?>
                        <a href="support.php" class="text-gray-500 hover:text-blue-600 px-1 pt-1 pb-4 text-sm font-medium">
                            Поддержка
                        </a>
                        <a href="#" class="text-blue-700 border-b-2 border-blue-600 px-1 pt-1 pb-4 text-sm font-medium">
                            О проекте
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if ($isLoggedIn): ?>
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
                        <a href="../auth/login.php" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            Войти
                        </a>
                        <a href="../auth/register.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                            Зарегистрироваться
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Героевый блок -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-5xl font-bold mb-6">AI Юрист</h1>
                <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Современная платформа для автоматизации юридических процессов и анализа документов
                </p>
                <div class="flex justify-center space-x-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold">5000+</div>
                        <div class="text-blue-200">Документов создано</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">1000+</div>
                        <div class="text-blue-200">Довольных клиентов</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">24/7</div>
                        <div class="text-blue-200">Поддержка</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Миссия -->
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Наша миссия</h2>
            <p class="text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                Сделать юридические услуги доступными для каждого бизнеса, 
                автоматизировать рутинные процессы и предоставить современные инструменты 
                для работы с юридическими документами на основе искусственного интеллекта.
            </p>
        </div>

        <!-- Возможности платформы -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Что мы предлагаем</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="mx-auto h-16 w-16 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-file-contract text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Генерация договоров</h3>
                    <p class="text-gray-600">
                        Автоматическое создание типовых договоров: аренды, поставки, 
                        оказания услуг, трудовых и других с учетом специфики вашего бизнеса.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="mx-auto h-16 w-16 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-search text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Анализ документов</h3>
                    <p class="text-gray-600">
                        Глубокий анализ юридических документов с выявлением рисков, 
                        неточностей и предоставлением рекомендаций по улучшению.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="mx-auto h-16 w-16 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-brain text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">AI-помощник</h3>
                    <p class="text-gray-600">
                        Интеллектуальный помощник, который объясняет сложные юридические 
                        термины простым языком и дает практические советы.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="mx-auto h-16 w-16 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Безопасность</h3>
                    <p class="text-gray-600">
                        Высокий уровень защиты данных с использованием современных 
                        методов шифрования и соблюдением требований безопасности.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="mx-auto h-16 w-16 bg-yellow-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-download text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Экспорт документов</h3>
                    <p class="text-gray-600">
                        Удобный экспорт готовых документов в формате DOCX 
                        с возможностью дальнейшего редактирования.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="mx-auto h-16 w-16 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-users text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Командная работа</h3>
                    <p class="text-gray-600">
                        Возможность совместной работы над документами в команде 
                        с контролем доступа и версионностью.
                    </p>
                </div>
            </div>
        </div>

        <!-- Технологии -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Наши технологии</h2>
            <div class="bg-white rounded-lg shadow-md p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Искусственный интеллект</h3>
                        <p class="text-gray-600 mb-4">
                            Используем современные модели машинного обучения для анализа 
                            и генерации юридических документов, включая GPT-4 и специализированные 
                            модели для правовой сферы.
                        </p>
                        <ul class="text-gray-600 space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Обработка естественного языка
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Семантический анализ документов
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Выявление юридических рисков
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Техническая платформа</h3>
                        <p class="text-gray-600 mb-4">
                            Надежная и масштабируемая архитектура, обеспечивающая 
                            высокую производительность и доступность сервиса.
                        </p>
                        <ul class="text-gray-600 space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Облачная инфраструктура
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                API для интеграций
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Адаптивный веб-интерфейс
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Команда -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Наша команда</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="mx-auto h-24 w-24 bg-blue-600 rounded-full flex items-center justify-center mb-6">
                        <span class="text-white text-2xl font-bold">АП</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Алексей Петров</h3>
                    <p class="text-blue-600 font-medium mb-4">CEO & Основатель</p>
                    <p class="text-gray-600">
                        10+ лет в IT, специалист по машинному обучению 
                        и обработке естественного языка.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="mx-auto h-24 w-24 bg-green-600 rounded-full flex items-center justify-center mb-6">
                        <span class="text-white text-2xl font-bold">МИ</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Мария Иванова</h3>
                    <p class="text-green-600 font-medium mb-4">Head of Legal</p>
                    <p class="text-gray-600">
                        Юрист с 15-летним опытом, специалист по корпоративному 
                        и договорному праву.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="mx-auto h-24 w-24 bg-purple-600 rounded-full flex items-center justify-center mb-6">
                        <span class="text-white text-2xl font-bold">ДС</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Дмитрий Сидоров</h3>
                    <p class="text-purple-600 font-medium mb-4">CTO</p>
                    <p class="text-gray-600">
                        Архитектор высоконагруженных систем, эксперт 
                        по безопасности и защите данных.
                    </p>
                </div>
            </div>
        </div>

        <!-- Контакты -->
        <div class="bg-blue-50 rounded-lg p-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Свяжитесь с нами</h2>
                <p class="text-xl text-gray-600 mb-8">
                    Готовы ответить на ваши вопросы и помочь оптимизировать юридические процессы
                </p>
                <div class="flex justify-center space-x-8">
                    <a href="mailto:info@ai-lawyer.ru" class="flex items-center text-blue-600 hover:text-blue-700">
                        <i class="fas fa-envelope mr-2"></i>
                        info@ai-lawyer.ru
                    </a>
                    <a href="tel:+74951234567" class="flex items-center text-blue-600 hover:text-blue-700">
                        <i class="fas fa-phone mr-2"></i>
                        +7 (495) 123-45-67
                    </a>
                    <a href="support.php" class="flex items-center text-blue-600 hover:text-blue-700">
                        <i class="fas fa-life-ring mr-2"></i>
                        Поддержка
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Меню пользователя
        const userMenuButton = document.getElementById('user-menu-button');
        if (userMenuButton) {
            userMenuButton.addEventListener('click', function() {
                const menu = document.getElementById('user-menu');
                menu.classList.toggle('hidden');
            });

            // Закрытие меню при клике вне его
            document.addEventListener('click', function(event) {
                const button = document.getElementById('user-menu-button');
                const menu = document.getElementById('user-menu');
                
                if (button && menu && !button.contains(event.target) && !menu.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html> 