<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/OpenAIAnalyzer.php';

class DocumentAnalyzer {
    private $conn;
    private $openAIAnalyzer;
    private $useAI;
    
    public function __construct($useAI = true) {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->useAI = $useAI;
        
        if ($this->useAI) {
            try {
                $this->openAIAnalyzer = new OpenAIAnalyzer();
            } catch (Exception $e) {
                // Если не удается инициализировать OpenAI, переключаемся на базовый анализ
                $this->useAI = false;
                error_log('OpenAI недоступен, переключение на базовый анализ: ' . $e->getMessage());
            }
        }
    }
    
    // Анализ загруженного документа
    public function analyzeDocument($documentText, $userId, $filename = '', $documentType = 'contract', $filePath = '') {
        try {
            $analysis = null;
            $aiPowered = false;
            
            // Попытка использовать ИИ-анализ
            if ($this->useAI && $this->openAIAnalyzer) {
                $aiResult = $this->openAIAnalyzer->analyzeDocument($documentText, $documentType);
                
                if ($aiResult['success']) {
                    $analysis = $this->formatAIAnalysis($aiResult['analysis']);
                    $aiPowered = true;
                } else {
                    // Если ИИ не сработал, логируем ошибку и переходим к базовому анализу
                    error_log('OpenAI Analysis failed: ' . ($aiResult['error'] ?? 'Unknown error'));
                }
            }
            
            // Если ИИ не доступен или не сработал, используем базовый анализ
            if (!$analysis) {
                $analysis = [
                    'risks' => $this->identifyRisks($documentText),
                    'suggestions' => $this->generateSuggestions($documentText),
                    'legal_issues' => $this->checkLegalIssues($documentText),
                    'clarity_issues' => $this->checkClarityIssues($documentText),
                    'completeness_score' => $this->calculateCompleteness($documentText),
                    'analyzed_at' => date('Y-m-d H:i:s'),
                    'ai_powered' => false
                ];
            }
            
            // Добавляем метаинформацию об анализе
            $analysis['ai_powered'] = $aiPowered;
            $analysis['analysis_type'] = $aiPowered ? 'ai_advanced' : 'rule_based';
            $analysis['document_type'] = $documentType;
            $analysis['analyzed_at'] = date('Y-m-d H:i:s');
            $analysis['original_text'] = $documentText; // Добавляем оригинальный текст для сохранения
            
            // Сохранение документа и анализа
            $documentId = $this->saveAnalyzedDocument($userId, $filename, $documentText, $analysis, $filePath);
            
            if ($documentId) {
                $this->logAction($userId, 'document_analyzed', [
                    'filename' => $filename,
                    'document_type' => $documentType,
                    'ai_powered' => $aiPowered,
                    'risks_count' => count($analysis['risks'] ?? []),
                    'suggestions_count' => count($analysis['suggestions'] ?? [])
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Документ успешно проанализирован' . ($aiPowered ? ' с помощью ИИ' : ''),
                    'document_id' => $documentId,
                    'analysis' => $analysis
                ];
            }
            
            return ['success' => false, 'message' => 'Ошибка при сохранении анализа'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Произошла ошибка: ' . $e->getMessage()];
        }
    }
    
    // Форматирование результатов ИИ-анализа для совместимости с базовым форматом
    private function formatAIAnalysis($aiAnalysis) {
        return [
            'risks' => $aiAnalysis['risks'] ?? [],
            'legal_violations' => $aiAnalysis['legal_violations'] ?? [],
            'unclear_terms' => $aiAnalysis['unclear_terms'] ?? [],
            'suggestions' => $aiAnalysis['suggestions'] ?? [],
            'completeness_score' => $aiAnalysis['completeness_score'] ?? 0,
            'missing_sections' => $aiAnalysis['missing_sections'] ?? [],
            'overall_assessment' => $aiAnalysis['overall_assessment'] ?? '',
            'key_strengths' => $aiAnalysis['key_strengths'] ?? [],
            'critical_issues' => $aiAnalysis['critical_issues'] ?? [],
            'ai_powered' => true
        ];
    }
    
    // Выявление рисков в документе
    private function identifyRisks($text) {
        $risks = [];
        $text_lower = mb_strtolower($text, 'UTF-8');
        
        // Проверка обязательных реквизитов
        if (!preg_match('/инн[\s\:]*\d{10,12}/', $text_lower)) {
            $risks[] = [
                'type' => 'missing_requisites',
                'severity' => 'high',
                'title' => 'Отсутствует ИНН',
                'description' => 'В договоре не указан ИНН одной или нескольких сторон, что может создать проблемы при взаимодействии с налоговыми органами.',
                'recommendation' => 'Добавьте ИНН всех юридических лиц и ИП в реквизиты договора.'
            ];
        }
        
        if (!preg_match('/огрн[\s\:]*\d{13,15}/', $text_lower)) {
            $risks[] = [
                'type' => 'missing_requisites',
                'severity' => 'medium',
                'title' => 'Отсутствует ОГРН',
                'description' => 'Не указан ОГРН юридического лица.',
                'recommendation' => 'Укажите ОГРН для юридических лиц в реквизитах.'
            ];
        }
        
        // Проверка сроков
        if (!preg_match('/срок[\s\w]*до[\s\d\.]*/', $text_lower) && !preg_match('/\d{1,2}\.\d{1,2}\.\d{4}/', $text)) {
            $risks[] = [
                'type' => 'missing_terms',
                'severity' => 'high',
                'title' => 'Не определены сроки',
                'description' => 'В договоре отсутствуют четкие сроки исполнения обязательств.',
                'recommendation' => 'Укажите конкретные даты или сроки выполнения обязательств.'
            ];
        }
        
        // Проверка ответственности
        if (!preg_match('/ответственность|штраф|пеня|неустойка/', $text_lower)) {
            $risks[] = [
                'type' => 'missing_liability',
                'severity' => 'medium',
                'title' => 'Не определена ответственность',
                'description' => 'Отсутствуют положения об ответственности сторон за нарушение договора.',
                'recommendation' => 'Добавьте раздел об ответственности сторон, включая штрафы и пени.'
            ];
        }
        
        // Проверка способов разрешения споров
        if (!preg_match('/суд|арбитраж|спор/', $text_lower)) {
            $risks[] = [
                'type' => 'missing_dispute_resolution',
                'severity' => 'medium',
                'title' => 'Не указан порядок разрешения споров',
                'description' => 'Отсутствуют положения о порядке разрешения споров между сторонами.',
                'recommendation' => 'Укажите подведомственность споров и досудебный порядок их урегулирования.'
            ];
        }
        
        // Проверка валютного законодательства
        if (preg_match('/доллар|евро|usd|eur|\$|€/', $text_lower)) {
            $risks[] = [
                'type' => 'currency_violation',
                'severity' => 'high',
                'title' => 'Возможное нарушение валютного законодательства',
                'description' => 'Обнаружены упоминания иностранной валюты, что может нарушать валютное законодательство РФ.',
                'recommendation' => 'Проверьте соответствие валютному законодательству РФ.'
            ];
        }
        
        // Проверка на неопределенные формулировки
        if (preg_match('/примерно|около|приблизительно|по возможности/', $text_lower)) {
            $risks[] = [
                'type' => 'vague_terms',
                'severity' => 'medium',
                'title' => 'Неопределенные формулировки',
                'description' => 'Обнаружены нечеткие формулировки, которые могут привести к спорам.',
                'recommendation' => 'Замените неопределенные формулировки на конкретные условия.'
            ];
        }
        
        return $risks;
    }
    
    // Генерация предложений по улучшению
    private function generateSuggestions($text) {
        $suggestions = [];
        $text_lower = mb_strtolower($text, 'UTF-8');
        
        // Предложения по структуре
        if (!preg_match('/предмет договора/', $text_lower)) {
            $suggestions[] = [
                'type' => 'structure',
                'title' => 'Добавить раздел "Предмет договора"',
                'description' => 'Рекомендуется четко выделить предмет договора в отдельном разделе.',
                'priority' => 'high'
            ];
        }
        
        if (!preg_match('/права и обязанности/', $text_lower)) {
            $suggestions[] = [
                'type' => 'structure',
                'title' => 'Добавить раздел "Права и обязанности сторон"',
                'description' => 'Необходимо четко определить права и обязанности каждой стороны.',
                'priority' => 'high'
            ];
        }
        
        // Предложения по содержанию
        if (preg_match('/\d+\s*рубл/', $text_lower)) {
            $suggestions[] = [
                'type' => 'formatting',
                'title' => 'Улучшить формат записи сумм',
                'description' => 'Рекомендуется указывать суммы цифрами и прописью для избежания разночтений.',
                'priority' => 'medium'
            ];
        }
        
        if (!preg_match('/форс[\-\s]*мажор/', $text_lower)) {
            $suggestions[] = [
                'type' => 'content',
                'title' => 'Добавить раздел о форс-мажоре',
                'description' => 'Рекомендуется включить положения о форс-мажорных обстоятельствах.',
                'priority' => 'medium'
            ];
        }
        
        return $suggestions;
    }
    
    // Проверка юридических проблем
    private function checkLegalIssues($text) {
        $issues = [];
        $text_lower = mb_strtolower($text, 'UTF-8');
        
        // Проверка соответствия ГК РФ
        if (preg_match('/процент.*\d+/', $text_lower)) {
            $percentMatch = preg_match('/(\d+[\.,]?\d*)\s*%/', $text);
            if ($percentMatch) {
                $issues[] = [
                    'article' => 'Статья 395 ГК РФ',
                    'issue' => 'Проверьте размер процентов за пользование чужими денежными средствами',
                    'description' => 'Размер процентов не должен превышать ключевую ставку ЦБ РФ.'
                ];
            }
        }
        
        // Проверка трудового законодательства
        if (preg_match('/трудов|работник|работодатель/', $text_lower)) {
            $issues[] = [
                'article' => 'Трудовой кодекс РФ',
                'issue' => 'Проверьте соответствие трудовому законодательству',
                'description' => 'Убедитесь, что договор не содержит условий, ухудшающих положение работника по сравнению с ТК РФ.'
            ];
        }
        
        return $issues;
    }
    
    // Проверка ясности формулировок
    private function checkClarityIssues($text) {
        $issues = [];
        $text_lower = mb_strtolower($text, 'UTF-8');
        
        // Длинные предложения
        $sentences = preg_split('/[.!?]+/', $text);
        foreach ($sentences as $sentence) {
            if (mb_strlen(trim($sentence), 'UTF-8') > 200) {
                $issues[] = [
                    'type' => 'long_sentence',
                    'text' => mb_substr(trim($sentence), 0, 100) . '...',
                    'issue' => 'Слишком длинное предложение',
                    'recommendation' => 'Разделите на несколько коротких предложений для лучшей читаемости.'
                ];
            }
        }
        
        // Сложные юридические термины без объяснений
        $complexTerms = ['цессия', 'новация', 'акцепт', 'оферта', 'индоссамент'];
        foreach ($complexTerms as $term) {
            if (preg_match('/\b' . preg_quote($term, '/') . '\b/i', $text)) {
                $issues[] = [
                    'type' => 'complex_term',
                    'term' => $term,
                    'issue' => 'Использование сложного юридического термина',
                    'recommendation' => 'Рассмотрите возможность добавления пояснения термина.'
                ];
            }
        }
        
        return $issues;
    }
    
    // Расчет полноты документа
    private function calculateCompleteness($text) {
        $score = 0;
        $maxScore = 100;
        $text_lower = mb_strtolower($text, 'UTF-8');
        
        // Проверка основных разделов (каждый +10 баллов)
        $requiredSections = [
            'предмет' => 15,
            'стоимость|цена|оплата' => 15,
            'срок' => 15,
            'права и обязанности' => 15,
            'ответственность' => 10,
            'разрешение споров' => 10,
            'заключительные положения' => 10,
            'реквизиты' => 10
        ];
        
        foreach ($requiredSections as $section => $points) {
            if (preg_match('/' . $section . '/', $text_lower)) {
                $score += $points;
            }
        }
        
        return min($score, $maxScore);
    }
    
    // Сохранение проанализированного документа
    private function saveAnalyzedDocument($userId, $filename, $content, $analysis, $filePath = '') {
        try {
            // Сохранение документа
            $documentData = [
                'original_content' => $content,
                'analysis' => $analysis,
                'analyzed_at' => date('Y-m-d H:i:s')
            ];
            
            $title = $filename ? "Анализ: " . $filename : 'Анализ документа - ' . date('d.m.Y H:i');
            
            // Используем правильную структуру таблицы user_documents
            $query = "INSERT INTO user_documents (user_id, title, document_type, original_filename, file_path, document_data, status) 
                     VALUES (:user_id, :title, 'analyzed', :filename, :file_path, :document_data, 'analyzed')";
            
            $stmt = $this->conn->prepare($query);
            $documentDataJson = json_encode($documentData, JSON_UNESCAPED_UNICODE);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':filename', $filename);
            $stmt->bindParam(':file_path', $filePath); // Полный путь к файлу
            $stmt->bindParam(':document_data', $documentDataJson); // Все данные в JSON
            
            if ($stmt->execute()) {
                $documentId = $this->conn->lastInsertId();
                
                // Также сохраняем анализ в отдельную таблицу (если таблица существует)
                try {
                    $this->saveAnalysis($documentId, $analysis);
                } catch (Exception $e) {
                    // Если таблица document_analysis не существует, это не критично
                    error_log("Не удалось сохранить в document_analysis: " . $e->getMessage());
                }
                
                return $documentId;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Ошибка сохранения документа: " . $e->getMessage());
            return false;
        }
    }
    
    // Сохранение результатов анализа
    private function saveAnalysis($documentId, $analysis) {
        try {
            $query = "INSERT INTO document_analysis (document_id, risks, suggestions, legal_issues, clarity_issues, analysis_status) 
                     VALUES (:document_id, :risks, :suggestions, :legal_issues, :clarity_issues, 'completed')";
            
            $stmt = $this->conn->prepare($query);
            $risksJson = json_encode($analysis['risks']);
            $suggestionsJson = json_encode($analysis['suggestions']);
            $legalIssuesJson = json_encode($analysis['legal_issues']);
            $clarityIssuesJson = json_encode($analysis['clarity_issues']);
            
            $stmt->bindParam(':document_id', $documentId);
            $stmt->bindParam(':risks', $risksJson);
            $stmt->bindParam(':suggestions', $suggestionsJson);
            $stmt->bindParam(':legal_issues', $legalIssuesJson);
            $stmt->bindParam(':clarity_issues', $clarityIssuesJson);
            $stmt->execute();
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Получение анализа документа
    public function getDocumentAnalysis($documentId, $userId) {
        try {
            $query = "SELECT da.*, ud.title, ud.document_data 
                     FROM document_analysis da 
                     JOIN user_documents ud ON da.document_id = ud.id 
                     WHERE da.document_id = :document_id AND ud.user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':document_id', $documentId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $result = $stmt->fetch();
                $result['risks'] = json_decode($result['risks'], true);
                $result['suggestions'] = json_decode($result['suggestions'], true);
                $result['legal_issues'] = json_decode($result['legal_issues'], true);
                $result['clarity_issues'] = json_decode($result['clarity_issues'], true);
                $result['document_data'] = json_decode($result['document_data'], true);
                return $result;
            }
            
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Логирование действий
    private function logAction($userId, $action, $details = []) {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            $query = "INSERT INTO action_logs (user_id, action, details, ip_address, user_agent) 
                     VALUES (:user_id, :action, :details, :ip_address, :user_agent)";
            
            $stmt = $this->conn->prepare($query);
            $detailsJson = json_encode($details);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':action', $action);
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