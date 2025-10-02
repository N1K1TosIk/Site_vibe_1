<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

class ContractGenerator {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Получение всех активных шаблонов
    public function getActiveTemplates() {
        try {
            $query = "SELECT id, name, category, description FROM contract_templates 
                     WHERE is_active = 1 ORDER BY category, name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Получение шаблона по ID
    public function getTemplateById($templateId) {
        try {
            $query = "SELECT * FROM contract_templates WHERE id = :id AND is_active = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $templateId);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $template = $stmt->fetch();
                // Декодирование JSON переменных
                $template['variables'] = json_decode($template['variables'], true);
                return $template;
            }
            
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Генерация договора из шаблона
    public function generateContract($templateId, $variables, $userId) {
        try {
            $template = $this->getTemplateById($templateId);
            if (!$template) {
                return ['success' => false, 'message' => 'Шаблон не найден'];
            }
            
            // Валидация обязательных переменных
            $missingVars = $this->validateVariables($template['variables'], $variables);
            if (!empty($missingVars)) {
                return [
                    'success' => false, 
                    'message' => 'Не заполнены обязательные поля: ' . implode(', ', $missingVars)
                ];
            }
            
            // Замена переменных в шаблоне
            $contractContent = $this->replaceVariables($template['template_content'], $variables);
            
            // Применение AI-улучшений (пока базовые правила)
            $contractContent = $this->applyAIEnhancements($contractContent, $template['category']);
            
            // Сохранение документа
            $documentData = [
                'template_id' => $templateId,
                'variables' => $variables,
                'generated_content' => $contractContent,
                'generation_date' => date('Y-m-d H:i:s')
            ];
            
            $documentId = $this->saveDocument(
                $userId,
                $template['name'] . ' - ' . date('d.m.Y'),
                'generated',
                $documentData,
                $templateId
            );
            
            if ($documentId) {
                $this->logAction($userId, 'contract_generated', [
                    'template_id' => $templateId,
                    'template_name' => $template['name']
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Договор успешно создан',
                    'document_id' => $documentId,
                    'content' => $contractContent
                ];
            }
            
            return ['success' => false, 'message' => 'Ошибка при сохранении документа'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Произошла ошибка: ' . $e->getMessage()];
        }
    }
    
    // Замена переменных в тексте шаблона
    private function replaceVariables($content, $variables) {
        foreach ($variables as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            
            // Форматирование дат
            if (strpos($key, 'date') !== false && !empty($value)) {
                // Если дата в формате YYYY-MM-DD, преобразуем в дд.мм.гггг
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $dateObj = DateTime::createFromFormat('Y-m-d', $value);
                    if ($dateObj) {
                        $value = $dateObj->format('d.m.Y');
                    }
                }
                // Если дата в формате YYYY-MM-DD HH:MM:SS, преобразуем в дд.мм.гггг
                elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
                    $dateObj = DateTime::createFromFormat('Y-m-d H:i:s', $value);
                    if ($dateObj) {
                        $value = $dateObj->format('d.m.Y');
                    }
                }
            }
            
            $content = str_replace($placeholder, $value, $content);
        }
        
        // Добавление текущей даты если не указана
        if (strpos($content, '{{current_date}}') !== false) {
            $currentDate = date('d.m.Y');
            $content = str_replace('{{current_date}}', $currentDate, $content);
        }
        
        // Автоматическое форматирование подписей
        $content = $this->formatSignatures($content, $variables);
        
        return $content;
    }
    
    // Форматирование подписей в формат "Фамилия И.О."
    private function formatSignatures($content, $variables) {
        // Паттерны для поиска линий подписей и замены их на ФИО
        $signaturePatterns = [
            // Универсальный паттерн для всех линий подписей
            '/<div style="margin-top:\s*30px;">\s*____________________\s*<\/div>/s' => function($matches) use ($variables, $content) {
                // Определяем контекст на основе предыдущего текста
                $beforeMatch = substr($content, 0, strpos($content, $matches[0]));
                
                // Ищем ключевые слова для определения типа подписи
                if (strpos($beforeMatch, 'РАБОТОДАТЕЛЬ') !== false && strrpos($beforeMatch, 'РАБОТОДАТЕЛЬ') > strrpos($beforeMatch, 'РАБОТНИК')) {
                    $name = $variables['employer_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'РАБОТНИК') !== false && strrpos($beforeMatch, 'РАБОТНИК') > strrpos($beforeMatch, 'РАБОТОДАТЕЛЬ')) {
                    $name = $variables['employee_name'] ?? '';
                } elseif (strpos($beforeMatch, 'АРЕНДОДАТЕЛЬ') !== false && strrpos($beforeMatch, 'АРЕНДОДАТЕЛЬ') > strrpos($beforeMatch, 'АРЕНДАТОР')) {
                    $name = $variables['landlord_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'АРЕНДАТОР') !== false && strrpos($beforeMatch, 'АРЕНДАТОР') > strrpos($beforeMatch, 'АРЕНДОДАТЕЛЬ')) {
                    $name = $variables['tenant_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'ИСПОЛНИТЕЛЬ') !== false && strrpos($beforeMatch, 'ИСПОЛНИТЕЛЬ') > strrpos($beforeMatch, 'ЗАКАЗЧИК')) {
                    $name = $variables['provider_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'ПОДРЯДЧИК') !== false && strrpos($beforeMatch, 'ПОДРЯДЧИК') > strrpos($beforeMatch, 'ЗАКАЗЧИК')) {
                    $name = $variables['contractor_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'ПРОДАВЕЦ') !== false && strrpos($beforeMatch, 'ПРОДАВЕЦ') > strrpos($beforeMatch, 'ПОКУПАТЕЛЬ')) {
                    $name = $variables['seller_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'ПОКУПАТЕЛЬ') !== false && strrpos($beforeMatch, 'ПОКУПАТЕЛЬ') > strrpos($beforeMatch, 'ПРОДАВЕЦ')) {
                    $name = $variables['buyer_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'ПОСТАВЩИК') !== false && strrpos($beforeMatch, 'ПОСТАВЩИК') > strrpos($beforeMatch, 'ПОКУПАТЕЛЬ')) {
                    $name = $variables['supplier_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'ЛИЗИНГОДАТЕЛЬ') !== false && strrpos($beforeMatch, 'ЛИЗИНГОДАТЕЛЬ') > strrpos($beforeMatch, 'ЛИЗИНГОПОЛУЧАТЕЛЬ')) {
                    $name = $variables['lessor_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'ЛИЗИНГОПОЛУЧАТЕЛЬ') !== false && strrpos($beforeMatch, 'ЛИЗИНГОПОЛУЧАТЕЛЬ') > strrpos($beforeMatch, 'ЛИЗИНГОДАТЕЛЬ')) {
                    $name = $variables['lessee_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'СТОРОНА, РАСКРЫВАЮЩАЯ ИНФОРМАЦИЮ') !== false && strrpos($beforeMatch, 'РАСКРЫВАЮЩАЯ') > strrpos($beforeMatch, 'ПОЛУЧАЮЩАЯ')) {
                    $name = $variables['disclosing_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'СТОРОНА, ПОЛУЧАЮЩАЯ ИНФОРМАЦИЮ') !== false && strrpos($beforeMatch, 'ПОЛУЧАЮЩАЯ') > strrpos($beforeMatch, 'РАСКРЫВАЮЩАЯ')) {
                    $name = $variables['receiving_representative'] ?? '';
                } elseif (strpos($beforeMatch, 'АВТОР') !== false && strrpos($beforeMatch, 'АВТОР') > strrpos($beforeMatch, 'ЗАКАЗЧИК')) {
                    $name = $variables['author_name'] ?? '';
                } elseif (strpos($beforeMatch, 'ЗАКАЗЧИК') !== false) {
                    $name = $variables['client_representative'] ?? '';
                } else {
                    $name = '';
                }
                
                return '<div style="margin-top: 30px;">' . $this->formatFullName($name) . '</div>';
            }
        ];
        
        foreach ($signaturePatterns as $pattern => $replacement) {
            if (is_callable($replacement)) {
                $content = preg_replace_callback($pattern, $replacement, $content);
            }
        }
        
        return $content;
    }
    
    // Форматирование ФИО в формат "Фамилия И.О."
    private function formatFullName($fullName) {
        if (empty($fullName)) {
            return '____________________';
        }
        
        // Убираем лишние пробелы и разбиваем на части
        $nameParts = preg_split('/\s+/', trim($fullName));
        
        if (count($nameParts) < 2) {
            return $fullName; // Если не полное ФИО, возвращаем как есть
        }
        
        $lastName = $nameParts[0]; // Фамилия
        $firstName = isset($nameParts[1]) ? mb_substr($nameParts[1], 0, 1, 'UTF-8') . '.' : '';
        $middleName = isset($nameParts[2]) ? mb_substr($nameParts[2], 0, 1, 'UTF-8') . '.' : '';
        
        return $lastName . ' ' . $firstName . $middleName;
    }
    
    // Валидация переменных
    private function validateVariables($templateVars, $userVars) {
        $missing = [];
        
        if (is_array($templateVars)) {
            foreach ($templateVars as $key => $description) {
                if (empty($userVars[$key])) {
                    $missing[] = $description;
                }
            }
        }
        
        return $missing;
    }
    
    // AI-улучшения (базовые правила для локального использования)
    private function applyAIEnhancements($content, $category) {
        // Базовые улучшения без внешних API
        
        // Проверка и добавление обязательных реквизитов для договоров РФ
        if (strpos($content, 'ИНН') === false && $category !== 'employment') {
            // Можно добавить предупреждение о необходимости указания ИНН
        }
        
        // Стандартизация формата дат
        $content = preg_replace('/(\d{1,2})\.(\d{1,2})\.(\d{4})/', '$1.$2.$3 г.', $content);
        
        // Улучшение читаемости сумм
        $content = preg_replace('/(\d+)\s*рубл/', '$1 (${1}) рубл', $content);
        
        // Добавление стандартных оговорок
        if (strpos($content, 'споры') === false) {
            $content .= "\n\n7. РАЗРЕШЕНИЕ СПОРОВ\n";
            $content .= "7.1. Все споры и разногласия, которые могут возникнуть между сторонами, ";
            $content .= "разрешаются путем переговоров. В случае невозможности достижения ";
            $content .= "соглашения споры разрешаются в судебном порядке в соответствии с ";
            $content .= "действующим законодательством Российской Федерации.";
        }
        
        return $content;
    }
    
    // Сохранение документа в базе данных
    private function saveDocument($userId, $title, $type, $documentData, $templateId = null) {
        try {
            $query = "INSERT INTO user_documents (user_id, title, document_type, template_id, document_data, status) 
                     VALUES (:user_id, :title, :document_type, :template_id, :document_data, 'completed')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':document_type', $type);
            $stmt->bindParam(':template_id', $templateId);
            $documentDataJson = json_encode($documentData);
            $stmt->bindParam(':document_data', $documentDataJson);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Получение документов пользователя
    public function getUserDocuments($userId, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT ud.*, ct.name as template_name 
                     FROM user_documents ud 
                     LEFT JOIN contract_templates ct ON ud.template_id = ct.id 
                     WHERE ud.user_id = :user_id 
                     ORDER BY ud.created_at DESC 
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Получение документа по ID
    public function getDocumentById($documentId, $userId) {
        try {
            $query = "SELECT ud.*, ct.name as template_name 
                     FROM user_documents ud 
                     LEFT JOIN contract_templates ct ON ud.template_id = ct.id 
                     WHERE ud.id = :id AND ud.user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $documentId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $document = $stmt->fetch();
                $document['document_data'] = json_decode($document['document_data'], true);
                return $document;
            }
            
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Экспорт документа в DOCX (базовая реализация)
    public function exportToDocx($documentId, $userId) {
        $document = $this->getDocumentById($documentId, $userId);
        if (!$document) {
            return false;
        }
        
        $content = $document['document_data']['generated_content'] ?? '';
        
        // Простая имитация DOCX - в реальности нужна библиотека PHPWord
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $document['title'] . '.docx"');
        
        echo $content; // Упрощенный экспорт
        return true;
    }
    
    // Логирование действий
    private function logAction($userId, $action, $details = []) {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            $query = "INSERT INTO action_logs (user_id, action, details, ip_address, user_agent) 
                     VALUES (:user_id, :action, :details, :ip_address, :user_agent)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':action', $action);
            $detailsJson = json_encode($details);
            $stmt->bindParam(':details', $detailsJson);
            $stmt->bindParam(':ip_address', $ip);
            $stmt->bindParam(':user_agent', $userAgent);
            $stmt->execute();
        } catch (Exception $e) {
            // Логирование не критично
        }
    }
}
?> 