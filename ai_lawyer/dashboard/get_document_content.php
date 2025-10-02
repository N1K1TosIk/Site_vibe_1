<?php
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/ContractGenerator.php';
require_once __DIR__ . '/../vendor/autoload.php';

use AILawyer\Classes\DocumentProcessor;

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

if (!$data || !isset($data['document_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Не указан ID документа']);
    exit;
}

$documentId = (int)$data['document_id'];

try {
    $contractGenerator = new ContractGenerator();
    $document = $contractGenerator->getDocumentById($documentId, $_SESSION['user_id']);
    
    if (!$document) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Документ не найден']);
        exit;
    }
    
    // Извлекаем содержимое документа через новый HTML-предпросмотр
    $content = '';
    $filePath = '';
    
    // Определяем путь к файлу
    if (isset($document['file_path']) && !empty($document['file_path'])) {
        $filePath = $document['file_path'];
    } elseif (isset($document['document_data']['file_path'])) {
        $filePath = $document['document_data']['file_path'];
    } else {
        // Если нет файла, используем старое содержимое
        if (isset($document['document_data']['generated_content'])) {
            $content = $document['document_data']['generated_content'];
        } else {
            $content = $document['content'] ?? 'Содержимое документа недоступно';
        }
    }
    
    // Если есть файл, извлекаем HTML-предпросмотр
    if (!empty($filePath) && file_exists($filePath)) {
        try {
            $processor = new DocumentProcessor();
            $result = $processor->processDocumentAsHTML($filePath);
            
            if ($result->isSuccess()) {
                $content = $result->getContent();
            } else {
                $errors = $result->getErrors();
                error_log("Document HTML preview failed: " . implode(", ", $errors));
                $content = 'Ошибка предпросмотра документа';
            }
        } catch (Exception $e) {
            error_log("Document HTML preview exception: " . $e->getMessage());
            $content = 'Ошибка предпросмотра документа: ' . $e->getMessage();
        }
    }
    
    // Возвращаем данные документа
    echo json_encode([
        'success' => true,
        'document' => [
            'id' => $document['id'],
            'title' => $document['title'],
            'content' => $content,
            'type' => $document['document_type'],
            'created_at' => $document['created_at'],
            'template_name' => $document['template_name'] ?? null,
            'file_path' => $filePath
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера: ' . $e->getMessage()]);
}
?> 