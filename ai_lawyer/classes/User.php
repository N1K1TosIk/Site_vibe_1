<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

class User {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Регистрация пользователя
    public function register($email, $password, $firstName, $lastName, $company = '', $phone = '') {
        try {
            // Проверка существующего email
            if ($this->emailExists($email)) {
                return ['success' => false, 'message' => 'Пользователь с таким email уже существует'];
            }
            
            // Валидация email
            if (!Security::validateEmail($email)) {
                return ['success' => false, 'message' => 'Некорректный email адрес'];
            }
            
            // Валидация пароля
            $passwordErrors = Security::validatePassword($password);
            if (!empty($passwordErrors)) {
                return ['success' => false, 'message' => implode('. ', $passwordErrors)];
            }
            
            // Хеширование пароля
            $passwordHash = Security::hashPassword($password);
            
            // Генерация токена верификации
            $verificationToken = bin2hex(random_bytes(32));
            
            $query = "INSERT INTO users (email, password_hash, first_name, last_name, company, phone, verification_token) 
                     VALUES (:email, :password_hash, :first_name, :last_name, :company, :phone, :verification_token)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $passwordHash);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':company', $company);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':verification_token', $verificationToken);
            
            if ($stmt->execute()) {
                $this->logAction($this->conn->lastInsertId(), 'user_registered', [
                    'email' => $email,
                    'company' => $company
                ]);
                
                return [
                    'success' => true, 
                    'message' => 'Регистрация успешна',
                    'user_id' => $this->conn->lastInsertId()
                ];
            }
            
            return ['success' => false, 'message' => 'Ошибка при регистрации'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Произошла ошибка: ' . $e->getMessage()];
        }
    }
    
    // Авторизация пользователя
    public function login($email, $password, $rememberMe = false) {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            
            // Проверка rate limiting
            // if (!Security::checkRateLimit($ip, 'login')) {
            //     return ['success' => false, 'message' => 'Слишком много попыток входа. Попробуйте позже.'];
            // }
            
            $query = "SELECT id, email, password_hash, first_name, last_name, company, role, status, failed_login_attempts 
                     FROM users WHERE email = :email AND status = 'active'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch();
                
                // Проверка блокировки аккаунта
                if ($user['failed_login_attempts'] >= 5) {
                    return ['success' => false, 'message' => 'Аккаунт заблокирован из-за множественных неудачных попыток входа'];
                }
                
                if (Security::verifyPassword($password, $user['password_hash'])) {
                    // Успешная авторизация
                    $this->resetFailedAttempts($user['id']);
                    $this->updateLastLogin($user['id']);
                    $this->createSession($user['id'], $rememberMe);
                    
                    $this->logAction($user['id'], 'user_login', ['ip' => $ip]);
                    
                    return [
                        'success' => true,
                        'message' => 'Успешная авторизация',
                        'user' => [
                            'id' => $user['id'],
                            'email' => $user['email'],
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'company' => $user['company'],
                            'role' => $user['role']
                        ]
                    ];
                } else {
                    // Неверный пароль
                    $this->incrementFailedAttempts($user['id']);
                    $this->logAction($user['id'], 'failed_login', ['ip' => $ip]);
                    return ['success' => false, 'message' => 'Неверный email или пароль'];
                }
            }
            
            return ['success' => false, 'message' => 'Неверный email или пароль'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Произошла ошибка: ' . $e->getMessage()];
        }
    }
    
    // Проверка существования email
    private function emailExists($email) {
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    // Сброс неудачных попыток
    private function resetFailedAttempts($userId) {
        $query = "UPDATE users SET failed_login_attempts = 0 WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }
    
    // Увеличение счетчика неудачных попыток
    private function incrementFailedAttempts($userId) {
        $query = "UPDATE users SET failed_login_attempts = failed_login_attempts + 1 WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }
    
    // Обновление времени последнего входа
    private function updateLastLogin($userId) {
        $query = "UPDATE users SET last_login = NOW() WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }
    
    // Создание сессии
    private function createSession($userId, $rememberMe = false) {
        // Сессия уже должна быть активна, не нужно повторно стартовать
        $sessionId = session_id();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Время истечения сессии
        $expiresAt = $rememberMe ? 
            date('Y-m-d H:i:s', strtotime('+30 days')) : 
            date('Y-m-d H:i:s', strtotime('+1 day'));
        
        // Удаление старых сессий пользователя
        $query = "DELETE FROM user_sessions WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        // Создание новой сессии
        $query = "INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, expires_at) 
                 VALUES (:user_id, :session_id, :ip_address, :user_agent, :expires_at)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':session_id', $sessionId);
        $stmt->bindParam(':ip_address', $ip);
        $stmt->bindParam(':user_agent', $userAgent);
        $stmt->bindParam(':expires_at', $expiresAt);
        $stmt->execute();
        
        // Установка переменных сессии
        $_SESSION['user_id'] = $userId;
        $_SESSION['logged_in'] = true;
    }
    
    // Выход из системы
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logAction($_SESSION['user_id'], 'user_logout');
            
            // Удаление сессии из БД
            $sessionId = session_id();
            $query = "DELETE FROM user_sessions WHERE session_id = :session_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':session_id', $sessionId);
            $stmt->execute();
        }
        
        // Очистка сессии
        session_unset();
        session_destroy();
        
        return ['success' => true, 'message' => 'Выход выполнен успешно'];
    }
    
    // Проверка активной сессии
    public function checkSession() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
            return false;
        }
        
        $sessionId = session_id();
        $userId = $_SESSION['user_id'];
        
        $query = "SELECT us.*, u.status FROM user_sessions us 
                 JOIN users u ON us.user_id = u.id 
                 WHERE us.session_id = :session_id AND us.user_id = :user_id 
                 AND us.expires_at > NOW() AND u.status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':session_id', $sessionId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            // Обновление времени активности
            $query = "UPDATE user_sessions SET last_activity = NOW() WHERE session_id = :session_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':session_id', $sessionId);
            $stmt->execute();
            
            return true;
        }
        
        // Неактивная сессия - очистка
        session_unset();
        session_destroy();
        return false;
    }
    
    // Получение данных пользователя
    public function getUserData($userId) {
        $query = "SELECT id, email, first_name, last_name, company, phone, role, created_at 
                 FROM users WHERE id = :user_id AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Логирование действий
    private function logAction($userId, $action, $details = []) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $detailsJson = json_encode($details);
        
        $query = "INSERT INTO action_logs (user_id, action, details, ip_address, user_agent) 
                 VALUES (:user_id, :action, :details, :ip_address, :user_agent)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':details', $detailsJson);
        $stmt->bindParam(':ip_address', $ip);
        $stmt->bindParam(':user_agent', $userAgent);
        $stmt->execute();
    }
    
    // Обновление профиля пользователя
    public function updateProfile($userId, $firstName, $lastName, $email, $company = '', $phone = '') {
        try {
            // Проверка уникальности email (если изменился)
            $currentUser = $this->getUserData($userId);
            if ($currentUser['email'] !== $email && $this->emailExists($email)) {
                return ['success' => false, 'message' => 'Пользователь с таким email уже существует'];
            }
            
            // Валидация email
            if (!Security::validateEmail($email)) {
                return ['success' => false, 'message' => 'Некорректный email адрес'];
            }
            
            $query = "UPDATE users SET first_name = :first_name, last_name = :last_name, 
                     email = :email, company = :company, phone = :phone, updated_at = NOW() 
                     WHERE id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':company', $company);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':user_id', $userId);
            
            if ($stmt->execute()) {
                $this->logAction($userId, 'profile_updated', [
                    'email' => $email,
                    'company' => $company
                ]);
                
                return ['success' => true, 'message' => 'Профиль успешно обновлен'];
            }
            
            return ['success' => false, 'message' => 'Ошибка при обновлении профиля'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Произошла ошибка: ' . $e->getMessage()];
        }
    }
    
    // Смена пароля
    public function changePassword($userId, $currentPassword, $newPassword, $confirmPassword) {
        try {
            // Проверка совпадения нового пароля и подтверждения
            if ($newPassword !== $confirmPassword) {
                return ['success' => false, 'message' => 'Пароли не совпадают'];
            }
            
            // Валидация нового пароля
            $passwordErrors = Security::validatePassword($newPassword);
            if (!empty($passwordErrors)) {
                return ['success' => false, 'message' => implode('. ', $passwordErrors)];
            }
            
            // Получение текущего хеша пароля
            $query = "SELECT password_hash FROM users WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() != 1) {
                return ['success' => false, 'message' => 'Пользователь не найден'];
            }
            
            $user = $stmt->fetch();
            
            // Проверка текущего пароля
            if (!Security::verifyPassword($currentPassword, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Неверный текущий пароль'];
            }
            
            // Хеширование нового пароля
            $newPasswordHash = Security::hashPassword($newPassword);
            
            // Обновление пароля
            $query = "UPDATE users SET password_hash = :password_hash, updated_at = NOW() WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password_hash', $newPasswordHash);
            $stmt->bindParam(':user_id', $userId);
            
            if ($stmt->execute()) {
                $this->logAction($userId, 'password_changed');
                return ['success' => true, 'message' => 'Пароль успешно изменен'];
            }
            
            return ['success' => false, 'message' => 'Ошибка при изменении пароля'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Произошла ошибка: ' . $e->getMessage()];
        }
    }
    
    // Получение статистики пользователя
    public function getUserStats($userId) {
        try {
            $query = "SELECT 
                        COUNT(*) as total_documents,
                        SUM(CASE WHEN document_type = 'generated' THEN 1 ELSE 0 END) as generated_contracts,
                        SUM(CASE WHEN document_type = 'analyzed' THEN 1 ELSE 0 END) as analyzed_documents
                      FROM user_documents WHERE user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (Exception $e) {
            return [
                'total_documents' => 0,
                'generated_contracts' => 0,
                'analyzed_documents' => 0
            ];
        }
    }
    
    // Получение активных сессий
    public function getActiveSessions($userId) {
        try {
            $query = "SELECT session_id, ip_address, user_agent, created_at, last_activity, expires_at 
                     FROM user_sessions 
                     WHERE user_id = :user_id AND expires_at > NOW() 
                     ORDER BY last_activity DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Получение истории действий
    public function getRecentActions($userId, $limit = 10) {
        try {
            $query = "SELECT action, details, ip_address, user_agent, created_at 
                     FROM action_logs 
                     WHERE user_id = :user_id 
                     ORDER BY created_at DESC 
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Выход со всех устройств
    public function logoutAllDevices($userId) {
        try {
            // Удаление всех сессий пользователя кроме текущей
            $currentSessionId = session_id();
            $query = "DELETE FROM user_sessions WHERE user_id = :user_id AND session_id != :current_session";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':current_session', $currentSessionId);
            $stmt->execute();
            
            $deletedSessions = $stmt->rowCount();
            
            $this->logAction($userId, 'logout_all_devices', [
                'sessions_closed' => $deletedSessions
            ]);
            
            return [
                'success' => true, 
                'message' => "Закрыто сессий: {$deletedSessions}. Текущая сессия сохранена."
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Произошла ошибка: ' . $e->getMessage()];
        }
    }
    
    // Инициация сброса пароля
    public function initiatePasswordReset($email) {
        try {
            // Проверка существования пользователя
            $query = "SELECT id, first_name FROM users WHERE email = :email AND status = 'active'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                // Не раскрываем, существует ли email в системе
                return [
                    'success' => true, 
                    'message' => 'Если указанный email зарегистрирован в системе, на него будет отправлена ссылка для восстановления пароля.'
                ];
            }
            
            $user = $stmt->fetch();
            
            // Генерация токена сброса
            $resetToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Сохранение токена в базе данных
            $query = "UPDATE users SET reset_token = :token, reset_token_expires = :expires 
                     WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $resetToken);
            $stmt->bindParam(':expires', $expiresAt);
            $stmt->bindParam(':user_id', $user['id']);
            
            if ($stmt->execute()) {
                // Формирование ссылки восстановления
                $resetLink = $this->getBaseUrl() . "/auth/reset-password.php?token=" . $resetToken;
                
                // В реальном проекте здесь была бы отправка email
                // Для демо-версии сохраняем ссылку в логи
                $this->logAction($user['id'], 'password_reset_requested', [
                    'email' => $email,
                    'reset_link' => $resetLink,
                    'expires_at' => $expiresAt
                ]);
                
                // Имитация отправки email (для демо)
                return [
                    'success' => true,
                    'message' => 'Ссылка для восстановления пароля отправлена на ваш email. Проверьте также папку "Спам". Ссылка действительна в течение 1 часа.',
                    'demo_link' => $resetLink // В продакшене этого не должно быть
                ];
            }
            
            return ['success' => false, 'message' => 'Ошибка при создании запроса на восстановление'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Произошла ошибка: ' . $e->getMessage()];
        }
    }
    
    // Валидация токена сброса пароля
    public function validateResetToken($token) {
        try {
            $query = "SELECT id, email, first_name, reset_token_expires 
                     FROM users 
                     WHERE reset_token = :token AND status = 'active'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                return [
                    'valid' => false,
                    'message' => 'Недействительная ссылка восстановления. Возможно, она уже была использована или истек срок действия.'
                ];
            }
            
            $user = $stmt->fetch();
            
            // Проверка срока действия токена
            if (strtotime($user['reset_token_expires']) < time()) {
                return [
                    'valid' => false,
                    'message' => 'Срок действия ссылки истек. Запросите новую ссылку для восстановления пароля.'
                ];
            }
            
            return [
                'valid' => true,
                'user_id' => $user['id'],
                'email' => $user['email'],
                'first_name' => $user['first_name']
            ];
            
        } catch (Exception $e) {
            return [
                'valid' => false,
                'message' => 'Произошла ошибка при проверке токена: ' . $e->getMessage()
            ];
        }
    }
    
    // Сброс пароля по токену
    public function resetPassword($token, $newPassword) {
        try {
            // Валидация токена
            $tokenValidation = $this->validateResetToken($token);
            if (!$tokenValidation['valid']) {
                return ['success' => false, 'message' => $tokenValidation['message']];
            }
            
            // Валидация нового пароля
            $passwordErrors = Security::validatePassword($newPassword);
            if (!empty($passwordErrors)) {
                return ['success' => false, 'message' => implode('. ', $passwordErrors)];
            }
            
            // Хеширование нового пароля
            $passwordHash = Security::hashPassword($newPassword);
            
            // Обновление пароля и очистка токена
            $query = "UPDATE users SET 
                        password_hash = :password_hash,
                        reset_token = NULL,
                        reset_token_expires = NULL,
                        failed_login_attempts = 0,
                        updated_at = NOW()
                      WHERE id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password_hash', $passwordHash);
            $stmt->bindParam(':user_id', $tokenValidation['user_id']);
            
            if ($stmt->execute()) {
                // Удаление всех активных сессий пользователя (безопасность)
                $query = "DELETE FROM user_sessions WHERE user_id = :user_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':user_id', $tokenValidation['user_id']);
                $stmt->execute();
                
                $this->logAction($tokenValidation['user_id'], 'password_reset_completed', [
                    'email' => $tokenValidation['email']
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Пароль успешно изменен. Войдите в систему с новым паролем.'
                ];
            }
            
            return ['success' => false, 'message' => 'Ошибка при изменении пароля'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Произошла ошибка: ' . $e->getMessage()];
        }
    }
    
    // Получение базового URL сайта
    private function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = dirname(dirname($_SERVER['SCRIPT_NAME'])); // Убираем /classes из пути
        return $protocol . $host . $path;
    }
}
?> 