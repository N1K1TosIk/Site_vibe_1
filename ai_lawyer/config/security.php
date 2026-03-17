<?php
// Локальные секреты (не коммитить!): скопируйте security.local.php.example → security.local.php
if (file_exists(__DIR__ . '/security.local.php')) {
    require_once __DIR__ . '/security.local.php';
}

// ВАЖНО: session ini_set должны быть до session_start() и только если сессия не активна
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Для локального сервера без HTTPS
    ini_set('session.cookie_samesite', 'Strict');
}

// OpenAI API — задаётся через переменную окружения OPENAI_API_KEY (никогда не храните ключ в коде!)
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '');

class Security {
    // Соль для хеширования паролей
    public static $pepper = 'your_secret_pepper_here_change_this';
    
    // Настройки сессии
    public static function configureSession() {
        // Регенерация ID сессии
        if (session_status() === PHP_SESSION_ACTIVE) {
            $oldSessionId = session_id();
            session_regenerate_id(true);
            $newSessionId = session_id();
            
            // Если пользователь авторизован, обновляем session_id в базе данных
            if (isset($_SESSION['user_id']) && $oldSessionId !== $newSessionId) {
                try {
                    require_once __DIR__ . '/database.php';
                    $database = new Database();
                    $conn = $database->getConnection();
                    
                    $query = "UPDATE user_sessions SET session_id = :new_session_id 
                             WHERE session_id = :old_session_id AND user_id = :user_id";
                    
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':new_session_id', $newSessionId);
                    $stmt->bindParam(':old_session_id', $oldSessionId);
                    $stmt->bindParam(':user_id', $_SESSION['user_id']);
                    $stmt->execute();
                } catch (Exception $e) {
                    // Логируем ошибку, но не прерываем выполнение
                    error_log("Ошибка обновления session_id: " . $e->getMessage());
                }
            }
        }
    }
    
    // Безопасное хеширование пароля
    public static function hashPassword($password) {
        return password_hash($password . self::$pepper, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,      // 4 итерации
            'threads' => 3,        // 3 потока
        ]);
    }
    
    // Проверка пароля
    public static function verifyPassword($password, $hash) {
        return password_verify($password . self::$pepper, $hash);
    }
    
    // Генерация CSRF токена
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    // Проверка CSRF токена
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // Защита от XSS
    public static function sanitizeOutput($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    // Валидация email
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Проверка силы пароля
    public static function validatePassword($password) {
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = 'Пароль должен содержать минимум 8 символов';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Пароль должен содержать хотя бы одну заглавную букву';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Пароль должен содержать хотя бы одну строчную букву';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Пароль должен содержать хотя бы одну цифру';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Пароль должен содержать хотя бы один специальный символ';
        }
        return $errors;
    }
    
    // Rate limiting для защиты от брутфорса
    public static function checkRateLimit($ip, $action = 'login', $maxAttempts = 5, $timeWindow = 900) {
        $file = __DIR__ . "/../temp/rate_limit_{$action}.json";
        $data = [];
        
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true) ?: [];
        }
        
        $now = time();
        
        // Очистка старых записей
        foreach ($data as $key => $value) {
            if ($now - $value['time'] > $timeWindow) {
                unset($data[$key]);
            }
        }
        
        // Проверка лимита
        $attempts = array_filter($data, function($item) use ($ip) {
            return $item['ip'] === $ip;
        });
        
        if (count($attempts) >= $maxAttempts) {
            return false;
        }
        
        // Добавление новой попытки
        $data[] = ['ip' => $ip, 'time' => $now];
        
        // Создание директории если не существует
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($file, json_encode($data));
        return true;
    }
}
?> 