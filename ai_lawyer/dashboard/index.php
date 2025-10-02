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

// Получение статистики пользователя
$statistics = getDashboardStatistics($_SESSION['user_id']);
$recentDocuments = getRecentDocuments($_SESSION['user_id'], 5);
$recentActions = getRecentActions($_SESSION['user_id'], 10);

// Функция получения статистики для dashboard
function getDashboardStatistics($userId) {
    try {
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        
        // Общая статистика документов
        $query = "SELECT 
                    COUNT(*) as total_documents,
                    SUM(CASE WHEN document_type = 'generated' THEN 1 ELSE 0 END) as generated_contracts,
                    SUM(CASE WHEN document_type = 'analyzed' THEN 1 ELSE 0 END) as analyzed_documents,
                    COUNT(DISTINCT template_id) as unique_templates
                  FROM user_documents 
                  WHERE user_id = :user_id";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $docStats = $stmt->fetch();
        
        // Статистика по дням (последние 7 дней)
        $query = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as count,
                    SUM(CASE WHEN document_type = 'generated' THEN 1 ELSE 0 END) as generated_count
                  FROM user_documents 
                  WHERE user_id = :user_id 
                    AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  GROUP BY DATE(created_at)
                  ORDER BY date ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $dailyStats = $stmt->fetchAll();
        
        // Статистика действий
        $query = "SELECT COUNT(*) as total_actions 
                  FROM action_logs 
                  WHERE user_id = :user_id";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $actionStats = $stmt->fetch();
        
        // Статистика по шаблонам
        $query = "SELECT 
                    ct.name as template_name,
                    COUNT(*) as usage_count
                  FROM user_documents ud 
                  LEFT JOIN contract_templates ct ON ud.template_id = ct.id
                  WHERE ud.user_id = :user_id AND ud.document_type = 'generated'
                  GROUP BY ud.template_id, ct.name
                  ORDER BY usage_count DESC
                  LIMIT 5";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $templateStats = $stmt->fetchAll();
        
        return [
            'total_documents' => $docStats['total_documents'] ?? 0,
            'generated_contracts' => $docStats['generated_contracts'] ?? 0,
            'analyzed_documents' => $docStats['analyzed_documents'] ?? 0,
            'unique_templates' => $docStats['unique_templates'] ?? 0,
            'total_actions' => $actionStats['total_actions'] ?? 0,
            'daily_stats' => $dailyStats,
            'template_stats' => $templateStats
        ];
    } catch (Exception $e) {
        return [
            'total_documents' => 0,
            'generated_contracts' => 0,
            'analyzed_documents' => 0,
            'total_content_length' => 0,
            'total_actions' => 0,
            'daily_stats' => []
        ];
    }
}

// Функция получения последних документов
function getRecentDocuments($userId, $limit = 5) {
    try {
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT ud.id, ud.title, ud.document_type, ud.created_at,
                         ct.name as template_name
                  FROM user_documents ud
                  LEFT JOIN contract_templates ct ON ud.template_id = ct.id
                  WHERE ud.user_id = :user_id 
                  ORDER BY ud.created_at DESC 
                  LIMIT :limit";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// Функция получения последних действий
function getRecentActions($userId, $limit = 10) {
    try {
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT action, details, created_at 
                  FROM action_logs 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// Функции для UI
function getDocumentTypeName($type) {
    $types = [
        'generated' => 'Сгенерированный договор',
        'uploaded' => 'Загруженный документ',
        'analyzed' => 'Проанализированный документ'
    ];
    return $types[$type] ?? 'Документ';
}

function getDocumentTypeIcon($type) {
    $icons = [
        'lease' => 'fa-building',
        'service' => 'fa-handshake',
        'employment' => 'fa-user-tie',
        'nda' => 'fa-shield-alt',
        'supply' => 'fa-truck',
        'analysis' => 'fa-search'
    ];
    return $icons[$type] ?? 'fa-file-alt';
}

function getDocumentTypeColor($type) {
    $colors = [
        'lease' => 'blue',
        'service' => 'green',
        'employment' => 'purple',
        'nda' => 'red',
        'supply' => 'orange',
        'analysis' => 'indigo'
    ];
    return $colors[$type] ?? 'gray';
}

function getActionName($action) {
    $actions = [
        'user_login' => 'Вход в систему',
        'user_logout' => 'Выход из системы',
        'user_register' => 'Регистрация',
        'profile_updated' => 'Обновление профиля',
        'password_changed' => 'Смена пароля',
        'document_created' => 'Создание документа',
        'document_analyzed' => 'Анализ документа'
    ];
    return $actions[$action] ?? $action;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AI Юрист</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 pt-12">
    <?php include '_navbar.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Приветствие -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Добро пожаловать, <?= Security::sanitizeOutput($userData['first_name']) ?>!
            </h1>
            <p class="mt-2 text-gray-600">
                Ваш персональный помощник для работы с юридическими документами
            </p>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900"><?= $statistics['total_documents'] ?></div>
                        <div class="text-sm text-gray-600">Всего документов</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-file-contract text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900"><?= $statistics['generated_contracts'] ?></div>
                        <div class="text-sm text-gray-600">Создано договоров</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-search text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900"><?= $statistics['analyzed_documents'] ?></div>
                        <div class="text-sm text-gray-600">Анализов проведено</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100">
                        <i class="fas fa-layer-group text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900"><?= $statistics['unique_templates'] ?></div>
                        <div class="text-sm text-gray-600">Типов шаблонов</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Быстрые действия</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="generator.php" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors group">
                    <div class="p-3 rounded-full bg-blue-100 group-hover:bg-blue-200 mr-4">
                        <i class="fas fa-plus text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Создать договор</div>
                        <div class="text-sm text-gray-600">Генератор юридических документов</div>
                    </div>
                </a>
                
                <a href="analyzer.php" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors group">
                    <div class="p-3 rounded-full bg-green-100 group-hover:bg-green-200 mr-4">
                        <i class="fas fa-search text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Анализировать документ</div>
                        <div class="text-sm text-gray-600">AI-анализ юридических рисков</div>
                    </div>
                </a>
                
                <a href="documents.php" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors group">
                    <div class="p-3 rounded-full bg-purple-100 group-hover:bg-purple-200 mr-4">
                        <i class="fas fa-folder text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Мои документы</div>
                        <div class="text-sm text-gray-600">Управление документами</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Последние документы -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">Последние документы</h2>
                        <a href="documents.php" class="text-sm text-blue-600 hover:text-blue-800">
                            Показать все <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php if (empty($recentDocuments)): ?>
                        <div class="p-6 text-center">
                            <i class="fas fa-file-alt text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Документов пока нет</p>
                            <a href="generator.php" class="mt-2 inline-block text-blue-600 hover:text-blue-800">
                                Создать первый документ
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentDocuments as $document): ?>
                            <div class="p-4 hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-<?= getDocumentTypeColor($document['document_type']) ?>-100 mr-3">
                                        <i class="fas <?= getDocumentTypeIcon($document['document_type']) ?> text-<?= getDocumentTypeColor($document['document_type']) ?>-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900 truncate">
                                            <?= Security::sanitizeOutput($document['title']) ?>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <?= $document['template_name'] ? Security::sanitizeOutput($document['template_name']) : getDocumentTypeName($document['document_type']) ?> • 
                                            <?= date('d.m.Y H:i', strtotime($document['created_at'])) ?>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i><?= date('H:i', strtotime($document['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Активность -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Последняя активность</h2>
                </div>
                <div class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
                    <?php if (empty($recentActions)): ?>
                        <div class="p-6 text-center">
                            <i class="fas fa-history text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Активности пока нет</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentActions as $action): ?>
                            <div class="p-4">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-gray-100 mr-3">
                                        <i class="fas fa-clock text-gray-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= getActionName($action['action']) ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?= date('d.m.Y H:i', strtotime($action['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Популярные шаблоны -->
        <?php if (!empty($statistics['template_stats'])): ?>
            <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Популярные шаблоны</h2>
                <div class="space-y-3">
                    <?php foreach ($statistics['template_stats'] as $template): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-blue-100 mr-3">
                                    <i class="fas fa-file-contract text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">
                                        <?= Security::sanitizeOutput($template['template_name'] ?? 'Неизвестный шаблон') ?>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        Использований: <?= $template['usage_count'] ?>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?= min(100, ($template['usage_count'] / max(1, $statistics['template_stats'][0]['usage_count'])) * 100) ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="generator.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i class="fas fa-plus mr-1"></i>Создать новый договор
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- График активности (если есть данные) -->
        <?php if (!empty($statistics['daily_stats'])): ?>
            <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Активность за последние 7 дней</h2>
                <div class="relative h-64">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        <?php endif; ?>

        <!-- Полезные ссылки -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Полезная информация</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4">
                    <i class="fas fa-question-circle text-blue-600 text-2xl mb-2"></i>
                    <h3 class="font-medium text-gray-900 mb-2">Часто задаваемые вопросы</h3>
                    <p class="text-sm text-gray-600 mb-3">Ответы на популярные вопросы о работе с системой</p>
                    <a href="support.php" class="text-sm text-blue-600 hover:text-blue-800">Читать FAQ</a>
                </div>
                
                <div class="bg-white rounded-lg p-4">
                    <i class="fas fa-graduation-cap text-green-600 text-2xl mb-2"></i>
                    <h3 class="font-medium text-gray-900 mb-2">Обучающие материалы</h3>
                    <p class="text-sm text-gray-600 mb-3">Изучите возможности AI-помощника для юристов</p>
                    <a href="about.php" class="text-sm text-green-600 hover:text-green-800">Узнать больше</a>
                </div>
                
                <div class="bg-white rounded-lg p-4">
                    <i class="fas fa-headset text-purple-600 text-2xl mb-2"></i>
                    <h3 class="font-medium text-gray-900 mb-2">Техническая поддержка</h3>
                    <p class="text-sm text-gray-600 mb-3">Нужна помощь? Обратитесь к нашей службе поддержки</p>
                    <a href="support.php" class="text-sm text-purple-600 hover:text-purple-800">Связаться</a>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($statistics['daily_stats'])): ?>
        <script>
            // График активности
            const ctx = document.getElementById('activityChart').getContext('2d');
            const dailyStats = <?= json_encode($statistics['daily_stats']) ?>;
            
            // Подготовка данных для графика
            const labels = [];
            const data = [];
            
            // Заполнение данных за последние 7 дней
            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                const dateStr = date.toISOString().split('T')[0];
                
                labels.push(date.toLocaleDateString('ru-RU', { month: 'short', day: 'numeric' }));
                
                const dayData = dailyStats.find(d => d.date === dateStr);
                data.push(dayData ? parseInt(dayData.count) : 0);
            }
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Документы',
                        data: data,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>
</body>
</html> 