<?php
session_start();
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/classes/User.php';

// Проверка авторизации
$user = new User();
$isLoggedIn = $user->checkSession();

// Если пользователь авторизован, перенаправляем в dashboard
if ($isLoggedIn) {
    header('Location: dashboard/index.php');
    exit;
}

// Только после проверки авторизации можно регенерировать ID
Security::configureSession();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Юрист - Умный помощник для юридических документов</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-white">
    <!-- Навигация -->
    <nav class="bg-white shadow-lg sticky top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-gavel text-blue-600 text-2xl mr-2"></i>
                        <span class="text-xl font-bold text-gray-700">AI Юрист</span>
                    </div>
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="#features" class="text-gray-500 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            Возможности
                        </a>
                        <a href="#pricing" class="text-gray-500 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            Тарифы
                        </a>
                        <a href="dashboard/about.php" class="text-gray-500 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            О проекте
                        </a>
                        <a href="dashboard/support.php" class="text-gray-500 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            Поддержка
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="auth/login.php" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Войти
                    </a>
                    <a href="auth/register.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                        Начать бесплатно
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Героевый блок -->
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h1 class="text-5xl font-bold mb-6 leading-tight">
                        Умный помощник для юридических документов
                    </h1>
                    <p class="text-xl text-blue-100 mb-8 leading-relaxed">
                        Создавайте договоры, анализируйте документы и получайте экспертные рекомендации 
                        с помощью искусственного интеллекта. Автоматизируйте юридические процессы 
                        и экономьте время.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a href="auth/register.php" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-lg font-medium rounded-md text-blue-700 bg-white hover:bg-gray-50 transition duration-200">
                            <i class="fas fa-rocket mr-2"></i>
                            Попробовать бесплатно
                        </a>
                        <a href="#demo" class="inline-flex items-center justify-center px-8 py-3 border-2 border-white text-lg font-medium rounded-md text-white hover:bg-white hover:text-blue-700 transition duration-200">
                            <i class="fas fa-play mr-2"></i>
                            Смотреть демо
                        </a>
                    </div>
                    <div class="flex items-center space-x-6 text-blue-100">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Бесплатный доступ</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <span>Безопасно</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-clock mr-2"></i>
                            <span>Быстрая настройка</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div class="bg-white rounded-lg shadow-2xl p-8 max-w-md w-full">
                        <div class="text-center mb-6">
                            <i class="fas fa-file-contract text-blue-600 text-4xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-900">Начните прямо сейчас</h3>
                            <p class="text-gray-600">Создайте аккаунт за 30 секунд</p>
                        </div>
                        <form action="auth/register.php" method="GET" class="space-y-4">
                            <input type="email" placeholder="Ваш email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="submit" 
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 font-medium transition duration-200">
                                Создать аккаунт
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 text-center mt-4">
                            Создавая аккаунт, вы соглашаетесь с условиями использования
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-blue-600 mb-2">5000+</div>
                    <div class="text-gray-600">Документов создано</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-green-600 mb-2">1000+</div>
                    <div class="text-gray-600">Довольных клиентов</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-purple-600 mb-2">95%</div>
                    <div class="text-gray-600">Точность анализа</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-orange-600 mb-2">24/7</div>
                    <div class="text-gray-600">Поддержка</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Возможности -->
    <div id="features" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Возможности платформы</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Полный набор инструментов для работы с юридическими документами
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300 p-8">
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-file-contract text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Генерация договоров</h3>
                    <p class="text-gray-600 mb-4">
                        Создавайте профессиональные договоры за считанные минуты. 
                        Множество шаблонов для разных сфер бизнеса.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Договоры аренды
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Трудовые договоры
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Договоры поставки
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300 p-8">
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-search text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Анализ документов</h3>
                    <p class="text-gray-600 mb-4">
                        Загружайте документы и получайте детальный анализ 
                        с выявлением рисков и рекомендациями.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Выявление рисков
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Проверка соответствия
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Рекомендации по улучшению
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300 p-8">
                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-brain text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">AI-консультант</h3>
                    <p class="text-gray-600 mb-4">
                        Получайте объяснения сложных юридических терминов 
                        и консультации по правовым вопросам.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Простые объяснения
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Правовые консультации
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Практические советы
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300 p-8">
                    <div class="h-12 w-12 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Безопасность</h3>
                    <p class="text-gray-600 mb-4">
                        Ваши данные защищены современными методами шифрования 
                        и соответствуют требованиям безопасности.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Шифрование данных
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Регулярные резервные копии
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Контроль доступа
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300 p-8">
                    <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-download text-yellow-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Экспорт документов</h3>
                    <p class="text-gray-600 mb-4">
                        Скачивайте готовые документы в удобных форматах 
                        для дальнейшего использования.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
            
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Формат DOCX
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Готов к печати
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300 p-8">
                    <div class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-users text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Командная работа</h3>
                    <p class="text-gray-600 mb-4">
                        Работайте над документами в команде с возможностью 
                        комментирования и контроля версий.
                    </p>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Совместное редактирование
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            Комментарии
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                            История изменений
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Тарифы -->
    <div id="pricing" class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Выберите подходящий тариф</h2>
                <p class="text-xl text-gray-600">
                    Начните бесплатно и масштабируйтесь по мере роста потребностей
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Бесплатный тариф -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="text-center">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Бесплатный</h3>
                        <div class="text-4xl font-bold text-gray-900 mb-4">0 ₽</div>
                        <p class="text-gray-600 mb-6">Для знакомства с платформой</p>
                    </div>
                    <ul class="space-y-3 mb-8 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            До 3 документов в месяц
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Базовый анализ
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            2 шаблона договоров
                        </li>
                    </ul>
                    <a href="auth/register.php" class="block w-full text-center py-3 px-4 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                        Начать бесплатно
                    </a>
                </div>

                <!-- Стандартный тариф -->
                <div class="bg-white rounded-lg shadow-lg border-2 border-blue-500 p-8 relative">
                    <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                        <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium">Популярный</span>
                    </div>
                    <div class="text-center">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Стандартный</h3>
                        <div class="text-4xl font-bold text-gray-900 mb-4">990 ₽</div>
                        <p class="text-gray-600 mb-6">Для малого бизнеса</p>
                    </div>
                    <ul class="space-y-3 mb-8 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            До 50 документов в месяц
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Полный анализ документов
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Все шаблоны договоров
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Экспорт в DOCX
                        </li>
                    </ul>
                    <a href="dashboard/pricing.php" class="block w-full text-center py-3 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                        Выбрать план
                    </a>
                </div>

                <!-- Профессиональный тариф -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="text-center">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Профессиональный</h3>
                        <div class="text-4xl font-bold text-gray-900 mb-4">2990 ₽</div>
                        <p class="text-gray-600 mb-6">Для юридических отделов</p>
                    </div>
                    <ul class="space-y-3 mb-8 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Безлимитные документы
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            AI-анализ с рекомендациями
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Приоритетная поддержка
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            API доступ
                        </li>
                    </ul>
                    <a href="dashboard/pricing.php" class="block w-full text-center py-3 px-4 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition duration-200">
                        Выбрать план
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA секция -->
    <div class="bg-blue-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Готовы автоматизировать юридические процессы?</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                Присоединяйтесь к тысячам компаний, которые уже используют AI Юрист для работы с документами
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="auth/register.php" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-lg font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50 transition duration-200">
                    <i class="fas fa-rocket mr-2"></i>
                    Начать бесплатно
                </a>
                <a href="dashboard/support.php" class="inline-flex items-center justify-center px-8 py-3 border-2 border-white text-lg font-medium rounded-md text-white hover:bg-white hover:text-blue-600 transition duration-200">
                    <i class="fas fa-phone mr-2"></i>
                    Связаться с нами
                </a>
            </div>
        </div>
    </div>

    <!-- Футер -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-gavel text-blue-400 text-xl mr-2"></i>
                        <span class="text-lg font-bold">AI Юрист</span>
                    </div>
                    <p class="text-gray-400">
                        Современная платформа для автоматизации юридических процессов
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Продукт</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-white">Возможности</a></li>
                        <li><a href="dashboard/pricing.php" class="hover:text-white">Тарифы</a></li>
                        <li><a href="dashboard/about.php" class="hover:text-white">О проекте</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Поддержка</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="dashboard/support.php" class="hover:text-white">Помощь</a></li>
                        <li><a href="mailto:support@ai-lawyer.ru" class="hover:text-white">Связаться с нами</a></li>
                        <li><a href="tel:+74951234567" class="hover:text-white">+7 (495) 123-45-67</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Следите за нами</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-telegram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-vk text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-youtube text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 AI Юрист. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <script>
        // Плавная прокрутка для якорных ссылок
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html> 