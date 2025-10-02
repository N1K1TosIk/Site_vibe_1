<?php
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/ContractGenerator.php';

// Проверка авторизации
$user = new User();
if (!$user->checkSession()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Не авторизован']);
    exit;
}

// Проверка Content-Type
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') === false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Неверный Content-Type']);
    exit;
}

// Получение данных из POST запроса
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['template_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Не указан ID шаблона']);
    exit;
}

$templateId = (int)$data['template_id'];

try {
    $contractGenerator = new ContractGenerator();
    $template = $contractGenerator->getTemplateById($templateId);
    
    if (!$template) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Шаблон не найден']);
        exit;
    }
    
    // Возвращаем поля шаблона
    echo json_encode([
        'success' => true,
        'template' => [
            'id' => $template['id'],
            'name' => $template['name'],
            'description' => $template['description']
        ],
        'variables' => $template['variables'] ?? []
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера: ' . $e->getMessage()]);
}
?> 