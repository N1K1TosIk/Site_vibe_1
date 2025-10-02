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

$error = '';
$success = '';

// Обработка формы обратной связи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'support') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Недействительный токен безопасности';
    } else {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';
        
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $error = 'Заполните все обязательные поля';
        } elseif (!Security::validateEmail($email)) {
            $error = 'Некорректный email адрес';
        } else {
            // В реальном проекте здесь была бы отправка на email службы поддержки
            $success = 'Ваше сообщение отправлено. Мы свяжемся с вами в ближайшее время.';
            
            // Логирование обращения
            if ($isLoggedIn) {
                $user->logAction($_SESSION['user_id'], 'support_message_sent', [
                    'subject' => $subject,
                    'email' => $email
                ]);
            }
        }
    }
}

$csrfToken = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поддержка - AI Юрист</title>
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
                        <a href="#" class="text-blue-700 border-b-2 border-blue-600 px-1 pt-1 pb-4 text-sm font-medium">
                            Поддержка
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Заголовок -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Поддержка пользователей</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Мы готовы помочь вам решить любые вопросы по работе с платформой
            </p>
        </div>

        <!-- Быстрые контакты -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="mx-auto h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Email поддержка</h3>
                <p class="text-gray-600 mb-4">Ответим в течение 24 часов</p>
                <a href="mailto:support@ai-lawyer.ru" class="text-blue-600 hover:text-blue-700 font-medium">
                    support@ai-lawyer.ru
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="mx-auto h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-phone text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Телефон</h3>
                <p class="text-gray-600 mb-4">Пн-Пт с 9:00 до 18:00 МСК</p>
                <a href="tel:+74951234567" class="text-green-600 hover:text-green-700 font-medium">
                    +7 (495) 123-45-67
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="mx-auto h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fab fa-telegram text-purple-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Telegram</h3>
                <p class="text-gray-600 mb-4">Быстрые ответы в чате</p>
                <a href="https://t.me/ai_lawyer_support" class="text-purple-600 hover:text-purple-700 font-medium">
                    @ai_lawyer_support
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- FAQ -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Часто задаваемые вопросы</h2>
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <button onclick="toggleFaq(1)" class="w-full px-6 py-4 text-left focus:outline-none focus:bg-gray-50">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-900">Как создать договор?</h3>
                                <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="faq-icon-1"></i>
                            </div>
                        </button>
                        <div class="hidden px-6 pb-4" id="faq-content-1">
                            <p class="text-gray-600">
                                Перейдите в раздел "Генератор договоров", выберите подходящий шаблон, 
                                заполните необходимые поля и нажмите "Создать договор". 
                                Система автоматически сгенерирует документ с учетом введенных данных.
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <button onclick="toggleFaq(2)" class="w-full px-6 py-4 text-left focus:outline-none focus:bg-gray-50">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-900">Как анализировать документы?</h3>
                                <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="faq-icon-2"></i>
                            </div>
                        </button>
                        <div class="hidden px-6 pb-4" id="faq-content-2">
                            <p class="text-gray-600">
                                В разделе "Анализ документов" загрузите файл в формате .docx, 
                                либо вставьте текст документа в поле. Система проанализирует содержимое 
                                и выделит потенциальные риски, неточности и предложит рекомендации.
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <button onclick="toggleFaq(3)" class="w-full px-6 py-4 text-left focus:outline-none focus:bg-gray-50">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-900">Какие форматы экспорта доступны?</h3>
                                <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="faq-icon-3"></i>
                            </div>
                        </button>
                        <div class="hidden px-6 pb-4" id="faq-content-3">
                            <p class="text-gray-600">
                                Созданные документы можно экспортировать в формате .docx. 
                                Для пользователей платных тарифов также доступны дополнительные 
                                возможности экспорта и интеграции с внешними системами.
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <button onclick="toggleFaq(4)" class="w-full px-6 py-4 text-left focus:outline-none focus:bg-gray-50">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-900">Безопасны ли мои данные?</h3>
                                <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="faq-icon-4"></i>
                            </div>
                        </button>
                        <div class="hidden px-6 pb-4" id="faq-content-4">
                            <p class="text-gray-600">
                                Мы используем современные методы шифрования и защиты данных. 
                                Все документы хранятся в зашифрованном виде, доступ к ним имеете только вы. 
                                Мы не передаем ваши данные третьим лицам без вашего согласия.
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <button onclick="toggleFaq(5)" class="w-full px-6 py-4 text-left focus:outline-none focus:bg-gray-50">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-900">Можно ли изменить тарифный план?</h3>
                                <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="faq-icon-5"></i>
                            </div>
                        </button>
                        <div class="hidden px-6 pb-4" id="faq-content-5">
                            <p class="text-gray-600">
                                Да, вы можете изменить тарифный план в любое время в разделе "Настройки" → "Тарифы". 
                                При повышении тарифа доступ к новым функциям предоставляется немедленно. 
                                При понижении изменения вступят в силу с следующего расчетного периода.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Форма обратной связи -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Связаться с нами</h2>
                
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
                
                <div class="bg-white rounded-lg shadow-md p-8">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="action" value="support">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Имя *</label>
                                <input type="text" id="name" name="name" required
                                       value="<?= $isLoggedIn ? Security::sanitizeOutput($userData['first_name'] . ' ' . $userData['last_name']) : '' ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" id="email" name="email" required
                                       value="<?= $isLoggedIn ? Security::sanitizeOutput($userData['email']) : '' ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Тема обращения *</label>
                            <select id="subject" name="subject" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Выберите тему</option>
                                <option value="technical">Техническая поддержка</option>
                                <option value="billing">Вопросы по оплате</option>
                                <option value="feature">Предложение по улучшению</option>
                                <option value="bug">Сообщение об ошибке</option>
                                <option value="other">Другое</option>
                            </select>
                        </div>
                        
                        <div class="mb-6">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Сообщение *</label>
                            <textarea id="message" name="message" rows="6" required
                                      placeholder="Опишите ваш вопрос или проблему подробно..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        
                        <button type="submit" 
                                class="w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-paper-plane mr-2"></i>Отправить сообщение
                        </button>
                    </form>
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

        // Переключение FAQ
        function toggleFaq(id) {
            const content = document.getElementById('faq-content-' + id);
            const icon = document.getElementById('faq-icon-' + id);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</body>
</html> 