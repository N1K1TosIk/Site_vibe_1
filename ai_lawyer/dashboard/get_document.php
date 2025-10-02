<?php
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';

// Проверка авторизации
$user = new User();
if (!$user->checkSession()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Не авторизован']);
    exit;
}

// Проверка CSRF токена
if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Недействительный токен безопасности']);
    exit;
}

$documentId = $_POST['document_id'] ?? 0;

if (!$documentId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Не указан ID документа']);
    exit;
}

try {
    require_once __DIR__ . '/../config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT title, content FROM user_documents 
              WHERE id = :document_id AND user_id = :user_id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':document_id', $documentId);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    $document = $stmt->fetch();
    
    if ($document) {
        echo json_encode([
            'success' => true,
            'title' => $document['title'],
            'content' => $document['content']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Документ не найден']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
}
?> 