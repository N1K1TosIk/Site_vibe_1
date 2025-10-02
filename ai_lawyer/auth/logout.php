<?php
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';

$user = new User();

// Выполнение выхода из системы
$result = $user->logout();

// Перенаправление на главную страницу
header('Location: ../index.php');
exit;
?> 