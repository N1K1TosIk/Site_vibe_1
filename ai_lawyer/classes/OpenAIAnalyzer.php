<?php
require_once __DIR__ . '/../config/security.php';

class OpenAIAnalyzer {
    private $apiKey;
    private $baseUrl = 'https://api.openai.com/v1/chat/completions';
    
    public function __construct() {
        $this->apiKey = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : null;
        if (!$this->apiKey) {
            throw new Exception('OpenAI API ключ не настроен');
        }
    }
    
    /**
     * Проведение полного юридического анализа документа
     */
    public function analyzeDocument($documentText, $documentType = 'contract') {
        try {
            // Подготовка промпта для анализа
            $prompt = $this->prepareLegalAnalysisPrompt($documentText, $documentType);
            
            // Отправка запроса к OpenAI
            $response = $this->makeOpenAIRequest($prompt);
            
            if ($response && isset($response['analysis'])) {
                return [
                    'success' => true,
                    'analysis' => $response['analysis'],
                    'ai_powered' => true,
                    'model' => 'gpt-4o',
                    'analyzed_at' => date('Y-m-d H:i:s')
                ];
            }
            
            return ['success' => false, 'error' => 'Не удалось получить анализ от ИИ'];
            
        } catch (Exception $e) {
            error_log('OpenAI Analysis Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Подготовка промпта для юридического анализа
     */
    private function prepareLegalAnalysisPrompt($documentText, $documentType) {
        $systemPrompt = "Ты - опытный российский юрист-аналитик, специализирующийся на анализе договоров и юридических документов в соответствии с законодательством РФ. 

Твоя задача - провести глубокий профессиональный анализ предоставленного документа и выявить:

1. ЮРИДИЧЕСКИЕ РИСКИ - потенциальные проблемы, которые могут привести к спорам или убыткам
2. НАРУШЕНИЯ ЗАКОНОДАТЕЛЬСТВА РФ - несоответствия действующему российскому праву
3. НЕЯСНЫЕ ФОРМУЛИРОВКИ - двусмысленные или нечеткие условия
4. ПРЕДЛОЖЕНИЯ ПО УЛУЧШЕНИЮ - конкретные рекомендации по доработке документа
5. ПОЛНОТА ДОКУМЕНТА - оценка наличия всех необходимых разделов и условий

ВАЖНО: Для каждого выявленного риска, нарушения или неясной формулировки ОБЯЗАТЕЛЬНО укажи в поле \"found_text\" точную фразу из документа, которая создает проблему. Это поможет пользователю быстро найти проблемное место в тексте.

Особое внимание уделяй:
- Соответствию Гражданскому кодексу РФ
- Трудовому законодательству (если применимо)
- Налоговому законодательству
- Валютному законодательству
- Антимонопольному законодательству
- Защите прав потребителей

Ответ должен быть структурированным в формате JSON со следующими полями:
{
  \"risks\": [
    {
      \"type\": \"тип_риска\",
      \"severity\": \"high|medium|low\", 
      \"title\": \"краткое описание\",
      \"description\": \"подробное описание риска\",
      \"legal_basis\": \"ссылка на статью закона\",
      \"recommendation\": \"как устранить риск\",
      \"found_text\": \"точная фраза из документа, которая создает риск\"
    }
  ],
  \"legal_violations\": [
    {
      \"law\": \"название закона/кодекса\",
      \"article\": \"номер статьи\",
      \"violation\": \"описание нарушения\", 
      \"consequence\": \"возможные последствия\",
      \"fix\": \"как исправить\",
      \"found_text\": \"точная фраза из документа, нарушающая закон\"
    }
  ],
  \"unclear_terms\": [
    {
      \"term\": \"неясная формулировка\",
      \"issue\": \"в чем проблема\",
      \"suggestion\": \"предлагаемая замена\",
      \"found_text\": \"точная найденная фраза из документа\"
    }
  ],
  \"suggestions\": [
    {
      \"type\": \"structure|content|formatting\",
      \"priority\": \"high|medium|low\",
      \"title\": \"название предложения\",
      \"description\": \"подробное описание\",
      \"implementation\": \"как реализовать\"
    }
  ],
  \"completeness_score\": число_от_0_до_100,
  \"missing_sections\": [\"список отсутствующих разделов\"],
  \"overall_assessment\": \"общая оценка документа\",
  \"key_strengths\": [\"сильные стороны документа\"],
  \"critical_issues\": [\"критические проблемы, требующие немедленного внимания\"]
}";
        
        $userPrompt = "Проанализируй следующий документ (тип: {$documentType}):\n\n{$documentText}\n\nПроведи глубокий юридический анализ в соответствии с российским законодательством.";
        
        return [
            'system' => $systemPrompt,
            'user' => $userPrompt
        ];
    }
    
    /**
     * Отправка запроса к OpenAI API
     */
    private function makeOpenAIRequest($prompt) {
        $data = [
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $prompt['system']
                ],
                [
                    'role' => 'user', 
                    'content' => $prompt['user']
                ]
            ],
            'temperature' => 0.3,
            'max_tokens' => 4000,
            'response_format' => ['type' => 'json_object']
        ];
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("CURL Error: " . $error);
        }
        
        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMessage = isset($errorData['error']['message']) ? $errorData['error']['message'] : 'Unknown API error';
            throw new Exception("OpenAI API Error (HTTP {$httpCode}): " . $errorMessage);
        }
        
        $decodedResponse = json_decode($response, true);
        
        if (!$decodedResponse || !isset($decodedResponse['choices'][0]['message']['content'])) {
            throw new Exception('Неверный формат ответа от OpenAI API');
        }
        
        $analysisJson = $decodedResponse['choices'][0]['message']['content'];
        $analysis = json_decode($analysisJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Ошибка парсинга JSON ответа: ' . json_last_error_msg());
        }
        
        return ['analysis' => $analysis];
    }
    
    /**
     * Генерация рекомендаций по конкретному типу документа
     */
    public function getDocumentTypeRecommendations($documentType) {
        $recommendations = [
            'contract' => [
                'required_sections' => [
                    'Стороны договора с полными реквизитами',
                    'Предмет договора',
                    'Права и обязанности сторон',
                    'Порядок расчетов',
                    'Ответственность сторон',
                    'Порядок разрешения споров',
                    'Срок действия договора',
                    'Заключительные положения'
                ],
                'key_checks' => [
                    'Наличие подписей и печатей',
                    'Указание реквизитов сторон (ИНН, ОГРН)',
                    'Соответствие валютному законодательству',
                    'Правильность указания НДС'
                ]
            ],
            'employment' => [
                'required_sections' => [
                    'Стороны трудового договора',
                    'Место работы',
                    'Трудовая функция',
                    'Дата начала работы',
                    'Условия оплаты труда',
                    'Режим рабочего времени и отдыха',
                    'Условия социального страхования'
                ],
                'key_checks' => [
                    'Соответствие минимальному размеру оплаты труда',
                    'Соблюдение максимальной продолжительности рабочего времени',
                    'Наличие обязательных гарантий и компенсаций'
                ]
            ],
            'lease' => [
                'required_sections' => [
                    'Предмет аренды с точным описанием',
                    'Размер арендной платы и порядок ее внесения',
                    'Срок аренды',
                    'Права и обязанности арендодателя',
                    'Права и обязанности арендатора',
                    'Порядок возврата имущества'
                ],
                'key_checks' => [
                    'Государственная регистрация (для недвижимости на срок более года)',
                    'Соответствие целевому назначению помещения',
                    'Правомочность арендодателя'
                ]
            ]
        ];
        
        return $recommendations[$documentType] ?? $recommendations['contract'];
    }
    
    /**
     * Проверка статуса API
     */
    public function checkAPIStatus() {
        try {
            $data = [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'user', 'content' => 'Тест подключения к API']
                ],
                'max_tokens' => 10
            ];
            
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                return ['status' => 'error', 'message' => $error];
            }
            
            if ($httpCode === 200) {
                return ['status' => 'ok', 'message' => 'API доступен'];
            } else {
                return ['status' => 'error', 'message' => "HTTP {$httpCode}"];
            }
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
?> 