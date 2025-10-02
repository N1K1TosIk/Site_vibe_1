<?php
// Конфигурация базы данных
class Database {
    private $host = 'localhost';
    private $db_name = 'ai_lawyer_db';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    public $pdo;

    public function getConnection() {
        if ($this->pdo === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                // Логируем ошибку
                error_log("Database connection error: " . $e->getMessage());
                
                // Показываем пользователю понятное сообщение
                if (strpos($e->getMessage(), 'Connection refused') !== false || 
                    strpos($e->getMessage(), 'отверг запрос на подключение') !== false) {
                    die('<div style="padding: 20px; background: #fee; border: 1px solid #fcc; color: #c33; font-family: Arial;">
                        <h3>Ошибка подключения к базе данных</h3>
                        <p><strong>MySQL сервер не запущен!</strong></p>
                        <p>Пожалуйста, запустите MySQL через XAMPP Control Panel:</p>
                        <ol>
                            <li>Откройте XAMPP Control Panel</li>
                            <li>Нажмите кнопку "Start" рядом с MySQL</li>
                            <li>Обновите эту страницу</li>
                        </ol>
                        <p><small>Техническая информация: ' . $e->getMessage() . '</small></p>
                    </div>');
                } else {
                    die('<div style="padding: 20px; background: #fee; border: 1px solid #fcc; color: #c33; font-family: Arial;">
                        <h3>Ошибка подключения к базе данных</h3>
                        <p>Не удалось подключиться к базе данных. Проверьте настройки подключения.</p>
                        <p><small>Техническая информация: ' . $e->getMessage() . '</small></p>
                    </div>');
                }
            }
        }
        
        return $this->pdo;
    }
}
?> 