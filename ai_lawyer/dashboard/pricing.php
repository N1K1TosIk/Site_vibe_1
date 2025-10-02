image.png<?php
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
    <title>Тарифные планы - AI Юрист</title>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Заголовок -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Тарифные планы</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Выберите подходящий план для работы с AI-помощником по юридическим документам
            </p>
        </div>

        <!-- Переключатель периода -->
        <div class="flex justify-center mb-8">
            <div class="bg-gray-100 p-1 rounded-lg">
                <button onclick="switchPeriod('monthly')" id="monthly-btn" class="px-4 py-2 text-sm font-medium rounded-md bg-white text-gray-900 shadow-sm">
                    Месяц
                </button>
                <button onclick="switchPeriod('yearly')" id="yearly-btn" class="px-4 py-2 text-sm font-medium rounded-md text-gray-700">
                    Год <span class="text-green-600 text-xs">-20%</span>
                </button>
            </div>
        </div>

        <!-- Тарифные планы -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Бесплатный план -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                <div class="text-center">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Бесплатный</h3>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">0 ₽</span>
                        <span class="text-gray-600">/месяц</span>
                    </div>
                    <p class="text-gray-600 mb-6">Для знакомства с платформой</p>
                </div>
                
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">До 3 договоров в месяц</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Базовый анализ документов</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">2 шаблона договоров</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times text-gray-400 mr-3"></i>
                        <span class="text-gray-400">Приоритетная поддержка</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times text-gray-400 mr-3"></i>

                    </li>
                </ul>
                
                <button class="w-full py-2 px-4 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition duration-200" disabled>
                    Текущий план
                </button>
            </div>

            <!-- Стандартный план -->
            <div class="bg-white rounded-lg shadow-lg border-2 border-blue-500 p-8 relative">
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium">Популярный</span>
                </div>
                
                <div class="text-center">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Стандартный</h3>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900 monthly-price">990 ₽</span>
                        <span class="text-4xl font-bold text-gray-900 yearly-price hidden">792 ₽</span>
                        <span class="text-gray-600">/месяц</span>
                    </div>
                    <p class="text-gray-600 mb-6">Для небольших компаний и ИП</p>
                </div>
                
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">До 50 договоров в месяц</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Полный анализ документов</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Все шаблоны договоров</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Экспорт в DOCX</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Email поддержка</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times text-gray-400 mr-3"></i>
                        <span class="text-gray-400">API доступ</span>
                    </li>
                </ul>
                
                <button onclick="selectPlan('standard')" class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                    Выбрать план
                </button>
            </div>

            <!-- Профессиональный план -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                <div class="text-center">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Профессиональный</h3>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900 monthly-price">2990 ₽</span>
                        <span class="text-4xl font-bold text-gray-900 yearly-price hidden">2392 ₽</span>
                        <span class="text-gray-600">/месяц</span>
                    </div>
                    <p class="text-gray-600 mb-6">Для юридических отделов</p>
                </div>
                
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Безлимитные договоры</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">AI-анализ с рекомендациями</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Кастомные шаблоны</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">API доступ</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Приоритетная поддержка</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span class="text-gray-700">Командная работа</span>
                    </li>
                </ul>
                
                <button onclick="selectPlan('professional')" class="w-full py-2 px-4 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition duration-200">
                    Выбрать план
                </button>
            </div>
        </div>

        <!-- Дополнительная информация -->
        <div class="mt-16">
            <div class="bg-blue-50 rounded-lg p-8">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Все планы включают:</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-shield-alt text-blue-600 text-xl mr-3"></i>
                            <span class="text-gray-700">Защита данных</span>
                        </div>
                        <div class="flex items-center justify-center">
                            <i class="fas fa-sync-alt text-blue-600 text-xl mr-3"></i>
                            <span class="text-gray-700">Автосохранение</span>
                        </div>
                        <div class="flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-blue-600 text-xl mr-3"></i>
                            <span class="text-gray-700">Мобильная версия</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Часто задаваемые вопросы</h2>
            <div class="max-w-3xl mx-auto space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Можно ли изменить план в любое время?</h3>
                    <p class="text-gray-600">Да, вы можете повысить или понизить тариф в любое время. Изменения вступают в силу с следующего расчетного периода.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Есть ли пробный период?</h3>
                    <p class="text-gray-600">Бесплатный план позволяет оценить основные возможности. Для платных планов доступен 14-дневный пробный период.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Какие способы оплаты доступны?</h3>
                    <p class="text-gray-600">Принимаем банковские карты, переводы и электронные кошельки. Для корпоративных клиентов доступна оплата по счету.</p>
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

        // Переключение периода оплаты
        function switchPeriod(period) {
            const monthlyBtn = document.getElementById('monthly-btn');
            const yearlyBtn = document.getElementById('yearly-btn');
            const monthlyPrices = document.querySelectorAll('.monthly-price');
            const yearlyPrices = document.querySelectorAll('.yearly-price');
            
            if (period === 'monthly') {
                monthlyBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                monthlyBtn.classList.remove('text-gray-700');
                yearlyBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                yearlyBtn.classList.add('text-gray-700');
                
                monthlyPrices.forEach(price => price.classList.remove('hidden'));
                yearlyPrices.forEach(price => price.classList.add('hidden'));
            } else {
                yearlyBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                yearlyBtn.classList.remove('text-gray-700');
                monthlyBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                monthlyBtn.classList.add('text-gray-700');
                
                monthlyPrices.forEach(price => price.classList.add('hidden'));
                yearlyPrices.forEach(price => price.classList.remove('hidden'));
            }
        }
        
        function selectPlan(plan) {
            <?php if (!$isLoggedIn): ?>
                window.location.href = '../auth/register.php';
            <?php else: ?>
                alert('Функция выбора тарифного плана будет доступна в следующих версиях.');
            <?php endif; ?>
        }
    </script>
</body>
</html> 