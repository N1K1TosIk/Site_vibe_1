<?php
// Инициализируем сессию (для хранения прогресса)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/Controllers/TaskController.php';
require_once __DIR__ . '/../config.php';

$action = $_GET['action'] ?? 'home';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($action) {
    case 'tasks':
        (new TaskController())->index();
        break;
    case 'task':
        if ($id === null) {
            header('Location: ' . BASE_URL . '/public/?action=tasks');
            exit;
        }
        (new TaskController())->show($id);
        break;
    case 'progress':
        require_once __DIR__ . '/Controllers/ProgressController.php';
        (new ProgressController())->index();
        break;
    case 'category':
        require_once __DIR__ . '/Controllers/CategoryController.php';
        $n = isset($_GET['n']) ? (int)$_GET['n'] : 0;
        (new CategoryController())->play($n);
        break;
    default:
        include __DIR__ . '/Views/home.php';
}
