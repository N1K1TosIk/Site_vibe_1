<?php
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';

// Подключаем автозагрузчик Composer для новой системы обработки документов
require_once __DIR__ . '/../vendor/autoload.php';
use AILawyer\Classes\DocumentProcessor;

// Проверка авторизации
$user = new User();
if (!$user->checkSession()) {
    header('Location: ../auth/login.php');
    exit;
}

Security::configureSession();

$userData = $user->getUserData($_SESSION['user_id']);
if (!$userData) {
    header('Location: ../auth/login.php');
    exit;
}

$success = '';
$error = '';
$analysisResult = null;

// Обработка загрузки и анализа документа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['analyze_document'])) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Недействительный токен безопасности';
    } else {
        $uploadResult = handleFileUpload();
        if ($uploadResult['success']) {
            // Получение типа документа из формы
            $documentType = $_POST['document_type'] ?? 'contract';
            $useAI = isset($_POST['use_ai']) && $_POST['use_ai'] === '1';
            
            $analysisResult = analyzeDocumentWithAI($uploadResult['file_path'], $uploadResult['file_type'], $documentType, $useAI);
            if ($analysisResult && $analysisResult['success']) {
                $success = $analysisResult['message'];
                $analysisResult = $analysisResult['analysis'];
            } else {
                $error = $analysisResult['message'] ?? 'Ошибка при анализе документа';
                $analysisResult = null;
            }
        } else {
            $error = $uploadResult['message'];
        }
    }
}

$csrfToken = Security::generateCSRFToken();

// Функция обработки загрузки файла
function handleFileUpload() {
    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Ошибка при загрузке файла'];
    }
    
    $file = $_FILES['document'];
            $allowedTypes = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Неподдерживаемый тип файла. Поддерживаются: DOCX, TXT'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'Файл слишком большой. Максимальный размер: 10MB'];
    }
    
    // Создание папки для загрузок
    $uploadDir = __DIR__ . '/../uploads/documents/' . $_SESSION['user_id'] . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Генерация уникального имени файла
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('doc_') . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return [
            'success' => true,
            'file_path' => $filePath,
            'original_name' => $file['name'],
            'file_type' => $file['type']
        ];
    } else {
        return ['success' => false, 'message' => 'Ошибка при сохранении файла'];
    }
}

// Функция анализа документа с ИИ
function analyzeDocumentWithAI($filePath, $fileType, $documentType = 'contract', $useAI = true) {
    // Извлечение текста из файла
    $text = extractTextFromFile($filePath, $fileType);
    if (!$text) {
        return ['success' => false, 'message' => 'Не удалось извлечь текст из файла'];
    }
    
    try {
        require_once __DIR__ . '/../classes/DocumentAnalyzer.php';
        $analyzer = new DocumentAnalyzer($useAI);
        
        $result = $analyzer->analyzeDocument($text, $_SESSION['user_id'], basename($filePath), $documentType, $filePath);
        
        // Добавляем информацию о файле в результаты анализа
        if ($result['success'] && isset($result['analysis'])) {
            $result['analysis']['file_path'] = $filePath;
            $result['analysis']['file_type'] = $fileType;
        }
        
        return $result;
        
    } catch (Exception $e) {
        error_log('Document analysis error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Ошибка при анализе документа: ' . $e->getMessage()];
    }
}

// Функция анализа документа (старая версия для совместимости)
function analyzeDocument($filePath, $fileType) {
    return analyzeDocumentWithAI($filePath, $fileType, 'contract', false);
}

// Извлечение текста из файла
function extractTextFromFile($filePath, $fileType) {
    try {
        $processor = new DocumentProcessor();
        $result = $processor->processDocument($filePath, ['output_format' => 'text']);
        
        if ($result->isSuccess()) {
            return $result->getContent();
        } else {
            // Логируем ошибки
            $errors = $result->getErrors();
            error_log("Document processing failed: " . implode(", ", $errors));
            return false;
        }
    } catch (Exception $e) {
        error_log("Document processing exception: " . $e->getMessage());
        return false;
    }
}

// Извлечение документа как HTML для отображения
function extractDocumentAsHTML($filePath, $fileType) {
    try {
        $processor = new DocumentProcessor();
        $result = $processor->processDocumentAsHTML($filePath);
        
        if ($result->isSuccess()) {
            return $result->getContent();
        } else {
            // Логируем ошибки
            $errors = $result->getErrors();
            error_log("HTML document processing failed: " . implode(", ", $errors));
            return false;
        }
    } catch (Exception $e) {
        error_log("HTML document processing exception: " . $e->getMessage());
        return false;
    }
}

// Универсальный предпросмотр документа (HTML)
function previewDocumentHTML($filePath, $fileType) {
    try {
        if (!file_exists($filePath)) {
            return '<div class="text-red-600">Файл не найден: ' . htmlspecialchars(basename($filePath)) . '</div>';
        }
        
        $processor = new DocumentProcessor();
        $result = $processor->processDocumentAsHTML($filePath);
        
        if ($result->isSuccess()) {
            $content = $result->getContent();
            return $content;
        } else {
            $errors = $result->getErrors();
            return '<div class="text-red-600">Ошибка предпросмотра документа: ' . implode(", ", $errors) . '</div>';
        }
    } catch (Exception $e) {
        return '<div class="text-red-600">Ошибка предпросмотра документа: ' . $e->getMessage() . '</div>';
    }
}

// Подсчет слов в тексте (поддерживает русский язык)
function countWords(string $text): int
{
    // Убираем JSON данные таблиц, но оставляем обычный текст
    $text = preg_replace('/TABLE_START\nTABLE_DATA:.*?\nTABLE_END/s', '', $text);
    
    // Убираем специальные символы и оставляем только буквы, цифры и пробелы
    $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
    
    // Убираем множественные пробелы
    $text = preg_replace('/\s+/', ' ', $text);
    
    // Разбиваем на слова и считаем
    $words = explode(' ', trim($text));
    
    // Фильтруем пустые элементы и короткие слова (меньше 1 символа)
    $words = array_filter($words, function($word) {
        $cleanWord = trim($word);
        return !empty($cleanWord) && mb_strlen($cleanWord) >= 1;
    });
    
    return count($words);
}

// Поиск юридических рисков
function findLegalRisks($text) {
    $risks = [];
    $riskPatterns = [
        'Отсутствие ответственности' => [
            'patterns' => ['без ответственности', 'не несет ответственность', 'освобождается от ответственности'],
            'severity' => 'high',
            'description' => 'Найдены формулировки, освобождающие от ответственности, что может создать правовые риски'
        ],
        'Неопределенные сроки' => [
            'patterns' => ['в разумные сроки', 'в кратчайшие сроки', 'своевременно', 'по возможности'],
            'severity' => 'medium',
            'description' => 'Обнаружены неконкретные временные формулировки, которые могут привести к спорам'
        ],
        'Односторонние условия' => [
            'patterns' => ['имеет право в одностороннем порядке', 'может изменить без согласования', 'по своему усмотрению'],
            'severity' => 'high',
            'description' => 'Найдены условия, дающие одной стороне чрезмерные права'
        ],
        'Финансовые санкции' => [
            'patterns' => ['штраф', 'пеня', 'неустойка', 'пени'],
            'severity' => 'low',
            'description' => 'Обнаружены штрафные санкции - проверьте их размер на соответствие закону'
        ]
    ];
    
    foreach ($riskPatterns as $riskType => $riskData) {
        foreach ($riskData['patterns'] as $pattern) {
            $pos = stripos($text, $pattern);
            if ($pos !== false) {
                // Находим контекст вокруг найденной фразы
                $contextStart = max(0, $pos - 50);
                $contextEnd = min(strlen($text), $pos + strlen($pattern) + 50);
                $context = substr($text, $contextStart, $contextEnd - $contextStart);
                
                $risks[] = [
                    'type' => $riskType,
                    'title' => $riskType,
                    'description' => $riskData['description'],
                    'severity' => $riskData['severity'],
                    'found_text' => $pattern,
                    'context' => trim($context),
                    'recommendation' => 'Рекомендуется конкретизировать или исключить данную формулировку'
                ];
                break; // Найдена одна фраза этого типа, переходим к следующему типу
            }
        }
    }
    
    return $risks;
}

// Поиск неясных формулировок
function findUnclearTerms($text) {
    $unclearTerms = [];
    $unclearPatterns = [
        'по возможности' => 'Укажите конкретные условия или сроки вместо "по возможности"',
        'в случае необходимости' => 'Определите четкие критерии "необходимости"',
        'при наличии возможности' => 'Конкретизируйте условия возможности',
        'в разумных пределах' => 'Укажите числовые границы или конкретные параметры',
        'существенное нарушение' => 'Дайте точное определение существенности нарушения',
        'значительный ущерб' => 'Определите минимальный размер ущерба в числовом выражении',
        'своевременно' => 'Укажите конкретные сроки вместо "своевременно"',
        'надлежащим образом' => 'Опишите конкретные требования к качеству исполнения'
    ];
    
    foreach ($unclearPatterns as $pattern => $suggestion) {
        $pos = stripos($text, $pattern);
        if ($pos !== false) {
            // Находим контекст вокруг найденной фразы
            $contextStart = max(0, $pos - 30);
            $contextEnd = min(strlen($text), $pos + strlen($pattern) + 30);
            $context = substr($text, $contextStart, $contextEnd - $contextStart);
            
            $unclearTerms[] = [
                'term' => $pattern,
                'issue' => 'Неопределенная формулировка может привести к разночтениям',
                'suggestion' => $suggestion,
                'found_text' => $pattern,
                'context' => trim($context)
            ];
        }
    }
    
    return $unclearTerms;
}

// Поиск нарушений законодательства
function findLegalViolations($text) {
    $violations = [];
    $violationPatterns = [
        'Трудовое право' => [
            'patterns' => ['работа без оплаты', 'без выходных', 'штраф за опоздание', 'удержание из зарплаты'],
            'law' => 'Трудовой кодекс РФ',
            'description' => 'Обнаружены условия, противоречащие трудовому законодательству'
        ],
        'Гражданское право' => [
            'patterns' => ['отказ от права на суд', 'полный отказ от ответственности', 'безвозмездная передача'],
            'law' => 'Гражданский кодекс РФ', 
            'description' => 'Найдены условия, нарушающие принципы гражданского права'
        ],
        'Потребительское право' => [
            'patterns' => ['возврат невозможен', 'претензии не принимаются', 'гарантия не распространяется'],
            'law' => 'Закон о защите прав потребителей',
            'description' => 'Обнаружены условия, ущемляющие права потребителей'
        ]
    ];
    
    foreach ($violationPatterns as $lawArea => $violationData) {
        foreach ($violationData['patterns'] as $pattern) {
            $pos = stripos($text, $pattern);
            if ($pos !== false) {
                // Находим контекст вокруг найденной фразы
                $contextStart = max(0, $pos - 50);
                $contextEnd = min(strlen($text), $pos + strlen($pattern) + 50);
                $context = substr($text, $contextStart, $contextEnd - $contextStart);
                
                $violations[] = [
                    'area' => $lawArea,
                    'law' => $violationData['law'],
                    'violation' => $violationData['description'],
                    'description' => $violationData['description'],
                    'found_text' => $pattern,
                    'context' => trim($context),
                    'severity' => 'high',
                    'consequence' => 'Данное условие может быть признано недействительным',
                    'fix' => 'Удалите или измените данную формулировку в соответствии с требованиями закона'
                ];
                break; // Найдена одна фраза этого типа, переходим к следующему типу
            }
        }
    }
    
    return $violations;
}

// Генерация предложений по улучшению
function generateSuggestions($text) {
    $suggestions = [];
    
    // Проверка наличия основных разделов договора
    $requiredSections = [
        'предмет договора' => 'Добавьте четкое описание предмета договора',
        'стороны' => 'Укажите полные реквизиты сторон',
        'срок действия' => 'Определите срок действия договора',
        'ответственность' => 'Пропишите ответственность сторон',
        'порядок разрешения споров' => 'Добавьте раздел о разрешении споров'
    ];
    
    foreach ($requiredSections as $section => $suggestion) {
        if (stripos($text, $section) === false) {
            $suggestions[] = [
                'type' => 'structure',
                'description' => $suggestion,
                'priority' => 'high'
            ];
        }
    }
    
    return $suggestions;
}

// Расчет показателя полноты документа
function calculateCompletenessScore($text) {
    $score = 0;
    $maxScore = 100;
    
    // Проверка длины документа
    $wordCount = countWords($text);
    if ($wordCount > 500) $score += 20;
    elseif ($wordCount > 200) $score += 10;
    
    // Проверка наличия ключевых разделов
    $keySections = ['предмет', 'стороны', 'срок', 'цена', 'ответственность'];
    foreach ($keySections as $section) {
        if (stripos($text, $section) !== false) {
            $score += 16; // 80 баллов за 5 разделов
        }
    }
    
    return min($score, $maxScore);
}

// Функция saveAnalysisResult удалена - теперь используется DocumentAnalyzer->saveAnalyzedDocument
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Анализ документов - AI Юрист</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
         <style>
 .document-preview { background: #fafbfc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; margin-bottom: 24px; overflow-x: auto; }
 
 .page-header { font-size: 13px; color: #888; margin-bottom: 8px; }
 .page-content { white-space: pre-line; }
 .text-content { font-family: 'Times New Roman', serif; font-size: 15px; color: #222; line-height: 1.6; }
 .docx-content { background: #f8f9fa; border-radius: 6px; padding: 20px; }
 .docx-content p { margin-bottom: 12px; text-align: justify; }
 .docx-content .numbered-item { margin-bottom: 16px; padding-left: 20px; }
 .docx-content .sub-item { margin-bottom: 12px; padding-left: 30px; }
 .docx-content .section-title { font-size: 18px; font-weight: bold; margin: 20px 0 12px 0; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
 .docx-content .subsection-title { font-size: 16px; font-weight: bold; margin: 16px 0 10px 0; color: #34495e; }
 .docx-content .centered-text { text-align: center; margin: 15px 0; }
 .table-container { margin: 20px 0; overflow-x: auto; }
 .document-table { width: 100%; border-collapse: collapse; border: 1px solid #ddd; }
 .document-table td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; vertical-align: top; }
 .document-table tr:nth-child(even) { background-color: #f9f9f9; }
 .professional-table-container { margin: 25px 0; }
 .professional-table { width: 100%; border-collapse: collapse; border: 2px solid #000; }
 .professional-table td { border: none; padding: 15px 20px; text-align: left; vertical-align: top; }
 .landlord-section { border-bottom: 1px solid #000; }
 .landlord-cell { padding-bottom: 20px; }
 .tenant-cell { padding-top: 20px; }
 .landlord-field, .tenant-field { margin-bottom: 8px; line-height: 1.4; }
 .landlord-field:last-child, .tenant-field:last-child { margin-bottom: 0; }
 .empty-cell { background-color: #f9f9f9; min-height: 20px; }
 .series-number-field { font-weight: 500; }
 .passport-table td { border: 1px solid #ddd; padding: 8px 10px; vertical-align: top; }
 .passport-table .table-header { font-weight: bold; background-color: #f8f9fa; }
 .table-cell { min-height: 20px; }
 .table-header { font-weight: bold; font-size: 14px; background-color: #f8f9fa; border-bottom: 1px solid #ddd; }
 .passport-field { font-weight: 500; }
 .issued-field { font-weight: 500; }
 .registered-field { font-weight: 500; }
 .signature-field { font-weight: 500; margin-top: 10px; }
 .landlord-cell { border-right: 1px solid #eee; }
 .tenant-cell { border-left: 1px solid #eee; }
 .dual-table-container { display: flex; gap: 20px; margin: 20px 0; }
 .table-wrapper { flex: 1; }
 .passport-table { width: 100%; border: 2px solid #000; }
 .passport-table td { border: none; padding: 8px 12px; text-align: left; vertical-align: top; }
 .table-header { font-weight: bold; background-color: #f8f9fa; }
 .passport-column { width: 50%; vertical-align: top; }
 .passport-section { padding: 10px; }
 .passport-line { margin-bottom: 8px; line-height: 1.4; }
 .passport-line:last-child { margin-bottom: 0; }
 .txt-content { background: #f8f9fa; border-radius: 6px; padding: 20px; }
 .txt-content p { margin-bottom: 12px; text-align: justify; }
 .ocr-content { background: #fff3cd; border: 1px solid #ffeaa7; }
 .ocr-notice { background: #fff3cd; color: #856404; padding: 8px 12px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; border-left: 4px solid #ffc107; }
 </style>
</head>
<body class="bg-gray-50 pt-12">
    <?php include '_navbar.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Анализ документов</h1>
            <p class="mt-2 text-gray-600">Загрузите документ для проведения юридического анализа с помощью AI</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-400 mr-2 mt-0.5"></i>
                    <p class="text-sm text-red-700"><?= Security::sanitizeOutput($error) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-400 mr-2 mt-0.5"></i>
                    <p class="text-sm text-green-700"><?= Security::sanitizeOutput($success) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Форма загрузки -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Загрузка документа</h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Тип документа
                            </label>
                            <select name="document_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="contract">Договор/Контракт</option>
                                <option value="employment">Трудовой договор</option>
                                <option value="lease">Договор аренды</option>
                                <option value="service">Договор оказания услуг</option>
                                <option value="purchase">Договор купли-продажи</option>
                                <option value="other">Другой документ</option>
                            </select>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Выберите файл для анализа
                            </label>
                            <div id="file-drop-zone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                                <div id="file-upload-default" class="space-y-1 text-center">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="document" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Выберите файл</span>
                                            <input id="document" name="document" type="file" class="sr-only" accept=".docx,.txt" required>
                                        </label>
                                        <p class="pl-1">или перетащите сюда</p>
                                    </div>
                                    <p class="text-xs text-gray-500">DOCX, TXT до 10MB</p>
                                </div>
                                
                                <div id="file-selected" class="space-y-1 text-center hidden">
                                    <i class="fas fa-file-alt text-4xl text-green-500"></i>
                                    <div class="text-sm text-gray-700">
                                        <span class="font-medium">Файл выбран:</span>
                                        <div id="selected-file-name" class="text-blue-600 font-semibold mt-1"></div>
                                        <div id="selected-file-size" class="text-xs text-gray-500 mt-1"></div>
                                    </div>
                                    <button type="button" id="change-file" class="text-xs text-blue-600 hover:text-blue-500">
                                        Изменить файл
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="ai-info-block" class="mb-6 p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg border border-purple-200">
                            <div class="flex items-center">
                                <input type="checkbox" id="use_ai" name="use_ai" value="1" checked class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="use_ai" class="ml-2 block text-sm text-gray-900">
                                    <span class="font-medium">Использовать ИИ-анализ</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                                        <i class="fas fa-robot mr-1"></i>GPT-4o
                                    </span>
                                </label>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">
                                ИИ проведет глубокий анализ с учетом российского законодательства и предоставит детальные рекомендации
                            </p>
                        </div>
                        
                        <button type="submit" name="analyze_document" class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-brain mr-2"></i>
                            Проанализировать с ИИ
                        </button>
                    </form>
                    
                    <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
                        <h3 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-robot text-purple-600 mr-2"></i>
                            ИИ-анализ включает:
                        </h3>
                        <div class="grid grid-cols-1 gap-2">
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-shield-alt text-red-500 mr-2"></i>
                                <span>Глубокий анализ юридических рисков</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-balance-scale text-blue-500 mr-2"></i>
                                <span>Соответствие российскому законодательству</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-search text-green-500 mr-2"></i>
                                <span>Выявление неясных формулировок</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                <span>Умные предложения по улучшению</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-chart-line text-purple-500 mr-2"></i>
                                <span>Оценка полноты и качества документа</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                                <span>Критические проблемы и их решения</span>
                            </div>
                        </div>
                        
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            <p class="text-xs text-gray-600 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                Анализ проводится с учетом ГК РФ, ТК РФ и другого российского законодательства
                            </p>
                        </div>
                    </div>
                    

                </div>
            </div>
            
            <!-- Результаты анализа -->
            <div class="lg:col-span-2">
                <?php if ($analysisResult): ?>
                    <div class="space-y-6">
                        <!-- Общая оценка -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold text-gray-900">Общая оценка документа</h2>
                                <?php if (isset($analysisResult['ai_powered']) && $analysisResult['ai_powered']): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-robot mr-1"></i>ИИ-анализ
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-cogs mr-1"></i>Базовый анализ
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (isset($analysisResult['overall_assessment']) && !empty($analysisResult['overall_assessment'])): ?>
                                <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                                    <h3 class="font-medium text-blue-900 mb-2">Общая оценка:</h3>
                                    <p class="text-blue-800"><?= Security::sanitizeOutput($analysisResult['overall_assessment']) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600"><?= $analysisResult['completeness_score'] ?>%</div>
                                    <div class="text-sm text-blue-700">Полнота документа</div>
                                </div>
                                <div class="text-center p-4 bg-red-50 rounded-lg">
                                    <div class="text-2xl font-bold text-red-600"><?= count($analysisResult['risks'] ?? []) ?></div>
                                    <div class="text-sm text-red-700">Юридические риски</div>
                                </div>
                                <div class="text-center p-4 bg-orange-50 rounded-lg">
                                    <div class="text-2xl font-bold text-orange-600"><?= count($analysisResult['legal_violations'] ?? $analysisResult['violations'] ?? []) ?></div>
                                    <div class="text-sm text-orange-700">Нарушения законов</div>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600"><?= count($analysisResult['suggestions'] ?? []) ?></div>
                                    <div class="text-sm text-green-700">Рекомендации</div>
                                </div>
                            </div>
                            
                            <?php if (isset($analysisResult['key_strengths']) && !empty($analysisResult['key_strengths'])): ?>
                                <div class="mt-4 p-4 bg-green-50 rounded-lg">
                                    <h3 class="font-medium text-green-900 mb-2">Сильные стороны документа:</h3>
                                    <ul class="text-sm text-green-800 space-y-1">
                                        <?php foreach ($analysisResult['key_strengths'] as $strength): ?>
                                            <li><i class="fas fa-check text-green-600 mr-2"></i><?= Security::sanitizeOutput($strength) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Информация о качестве извлеченного текста -->
                            <?php if (isset($analysisResult['original_text']) && !empty($analysisResult['original_text'])): ?>
                                <?php 
                                $originalText = $analysisResult['original_text'];
                                
                                // Правильный подсчет слов для русского текста
                                $wordCount = countWords($originalText);
                                
                                // Улучшенное определение русского текста
                                $russianChars = preg_match_all('/[а-яёА-ЯЁ]/u', $originalText, $matches);
                                $totalChars = strlen($originalText);
                                $hasRussianText = ($russianChars > 10) || ($russianChars > 0 && $totalChars > 0 && ($russianChars / $totalChars) > 0.05);
                                
                                // Расширенный список юридических терминов
                                $legalTerms = [
                                    'договор', 'контракт', 'соглашение', 'стороны', 'обязательства', 'права', 'ответственность',
                                    'условия', 'срок', 'платеж', 'оплата', 'поставка', 'аренда', 'купля', 'продажа',
                                    'закон', 'кодекс', 'статья', 'пункт', 'подпункт', 'часть', 'раздел', 'глава',
                                    'федеральный', 'российский', 'россия', 'москва', 'санкт-петербург', 'область', 'край',
                                    'истец', 'ответчик', 'суд', 'арбитраж', 'иск', 'претензия', 'штраф', 'пеня',
                                    'нкс', 'ндс', 'налог', 'бухгалтер', 'учет', 'отчетность', 'лицензия', 'разрешение'
                                ];
                                
                                $legalTermsFound = 0;
                                foreach ($legalTerms as $term) {
                                    if (stripos($originalText, $term) !== false) {
                                        $legalTermsFound++;
                                    }
                                }

                                ?>
                                
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <h3 class="font-medium text-gray-900 mb-2">Качество извлеченного текста:</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div class="text-center">
                                            <div class="font-bold text-blue-600"><?= $wordCount ?></div>
                                            <div class="text-gray-600">Слов</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold <?= $hasRussianText ? 'text-green-600' : 'text-red-600' ?>">
                                                <?= $hasRussianText ? 'Да' : 'Нет' ?>
                                            </div>
                                            <div class="text-gray-600">Русский текст</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold text-purple-600"><?= $legalTermsFound ?></div>
                                            <div class="text-gray-600">Юр. терминов</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold text-orange-600"><?= round(($legalTermsFound / max(1, $wordCount)) * 100, 1) ?>%</div>
                                            <div class="text-gray-600">Юр. плотность</div>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                                            <div class="text-center">
                                                <div class="font-bold text-gray-700"><?= strlen($originalText) ?></div>
                                                <div class="text-gray-500">Знаков (с пробелами)</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="font-bold text-gray-700"><?= strlen(preg_replace('/\s+/', '', $originalText)) ?></div>
                                                <div class="text-gray-500">Знаков (без пробелов)</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="font-bold text-gray-700"><?= substr_count($originalText, "\n") + 1 ?></div>
                                                <div class="text-gray-500">Строк</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endif; ?>
                        </div>

                        <!-- Содержимое документа -->
                        <?php if (isset($analysisResult['file_path']) && !empty($analysisResult['file_path'])): ?>
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                                    Содержимое документа
                                </h3>
                                

                                
                                <div class="document-preview">
                                    <?php 
                                    $htmlContent = previewDocumentHTML($analysisResult['file_path'], $analysisResult['file_type'] ?? 'docx');
                                    if ($htmlContent) {
                                        echo $htmlContent;
                                    } else {
                                        echo '<div class="text-red-600">Ошибка загрузки содержимого документа</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                                    Содержимое документа
                                </h3>
                                <div class="text-red-600">
                                    <p>Ошибка загрузки содержимого документа</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Юридические риски -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                Юридические риски (<?= count($analysisResult['risks'] ?? []) ?>)
                            </h3>
                            <?php if (empty($analysisResult['risks'])): ?>
                                <p class="text-green-600"><i class="fas fa-check-circle mr-2"></i>Серьезные юридические риски не обнаружены</p>
                            <?php else: ?>
                                <div class="space-y-4">
                                    <?php foreach ($analysisResult['risks'] as $risk): ?>
                                        <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="font-medium text-yellow-800 flex-1">
                                                    <?= Security::sanitizeOutput($risk['title'] ?? $risk['type'] ?? 'Юридический риск') ?>
                                                </div>
                                                <?php if (isset($risk['severity'])): ?>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                        <?= $risk['severity'] === 'high' ? 'bg-red-100 text-red-800' : 
                                                           ($risk['severity'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') ?>">
                                                        <?= $risk['severity'] === 'high' ? 'Высокий' : 
                                                           ($risk['severity'] === 'medium' ? 'Средний' : 'Низкий') ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-sm text-yellow-700 mb-3">
                                                <?= Security::sanitizeOutput($risk['description']) ?>
                                            </div>
                                            
                                            <!-- Подсветка проблемных фраз -->
                                            <?php if (isset($risk['found_text']) && !empty($risk['found_text'])): ?>
                                                <div class="text-xs text-red-600 mb-2 p-2 bg-red-50 border-l-2 border-red-300 rounded">
                                                    <i class="fas fa-search mr-1"></i>
                                                    <strong>Найденная фраза:</strong> 
                                                    <span class="font-mono bg-red-100 px-1 py-0.5 rounded text-red-800"><?= Security::sanitizeOutput($risk['found_text']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($risk['legal_basis']) && !empty($risk['legal_basis'])): ?>
                                                <div class="text-xs text-gray-600 mb-2">
                                                    <i class="fas fa-balance-scale mr-1"></i>
                                                    <strong>Правовая основа:</strong> <?= Security::sanitizeOutput($risk['legal_basis']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (isset($risk['recommendation']) && !empty($risk['recommendation'])): ?>
                                                <div class="text-sm text-blue-700 bg-blue-50 p-2 rounded">
                                                    <i class="fas fa-lightbulb mr-1"></i>
                                                    <strong>Рекомендация:</strong> <?= Security::sanitizeOutput($risk['recommendation']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Неясные формулировки -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-question-circle text-orange-500 mr-2"></i>
                                Неясные формулировки (<?= count($analysisResult['unclear_terms'] ?? []) ?>)
                            </h3>
                            <?php 
                            $unclearTerms = $analysisResult['unclear_terms'] ?? [];
                            if (empty($unclearTerms)): ?>
                                <p class="text-green-600"><i class="fas fa-check-circle mr-2"></i>Неясные формулировки не обнаружены</p>
                            <?php else: ?>
                                <div class="space-y-3">
                                    <?php foreach ($unclearTerms as $term): ?>
                                        <div class="p-3 border-l-4 border-orange-400 bg-orange-50">
                                            <div class="font-medium text-orange-800">"<?= Security::sanitizeOutput($term['term']) ?>"</div>
                                            <div class="text-sm text-orange-700 mb-2"><?= Security::sanitizeOutput($term['issue'] ?? $term['suggestion']) ?></div>
                                            
                                            <!-- Подсветка найденной фразы -->
                                            <?php if (isset($term['found_text']) && !empty($term['found_text'])): ?>
                                                <div class="text-xs text-orange-600 mb-2 p-2 bg-orange-100 border-l-2 border-orange-300 rounded">
                                                    <i class="fas fa-search mr-1"></i>
                                                    <strong>Найденная фраза:</strong> 
                                                    <span class="font-mono bg-orange-200 px-1 py-0.5 rounded text-orange-900"><?= Security::sanitizeOutput($term['found_text']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($term['suggestion']) && $term['issue'] !== $term['suggestion']): ?>
                                                <div class="text-sm text-blue-700 bg-blue-50 p-2 rounded">
                                                    <i class="fas fa-lightbulb mr-1"></i>
                                                    <strong>Рекомендация:</strong> <?= Security::sanitizeOutput($term['suggestion']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Критические проблемы (только для ИИ) -->
                        <?php if (isset($analysisResult['critical_issues']) && !empty($analysisResult['critical_issues'])): ?>
                            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                    Критические проблемы (<?= count($analysisResult['critical_issues']) ?>)
                                </h3>
                                <div class="space-y-3">
                                    <?php foreach ($analysisResult['critical_issues'] as $issue): ?>
                                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                            <div class="text-red-800 font-medium mb-2">
                                                <i class="fas fa-times-circle mr-2"></i>
                                                <?= Security::sanitizeOutput($issue) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Нарушения законодательства -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-balance-scale text-red-500 mr-2"></i>
                                Возможные нарушения законодательства (<?= count($analysisResult['legal_violations'] ?? $analysisResult['violations'] ?? []) ?>)
                            </h3>
                            <?php 
                            $violations = $analysisResult['legal_violations'] ?? $analysisResult['violations'] ?? [];
                            if (empty($violations)): ?>
                                <p class="text-green-600"><i class="fas fa-check-circle mr-2"></i>Нарушения законодательства не обнаружены</p>
                            <?php else: ?>
                                <div class="space-y-4">
                                    <?php foreach ($violations as $violation): ?>
                                        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                                            <div class="font-medium text-red-800 mb-2">
                                                <?php if (isset($violation['law'])): ?>
                                                    <i class="fas fa-gavel mr-2"></i>
                                                    <?= Security::sanitizeOutput($violation['law']) ?>
                                                    <?php if (isset($violation['article'])): ?>
                                                        - <?= Security::sanitizeOutput($violation['article']) ?>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?= Security::sanitizeOutput($violation['area'] ?? 'Нарушение законодательства') ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-sm text-red-700 mb-3">
                                                <?= Security::sanitizeOutput($violation['violation'] ?? $violation['description']) ?>
                                            </div>
                                            
                                            <!-- Подсветка проблемных фраз для нарушений -->
                                            <?php if (isset($violation['found_text']) && !empty($violation['found_text'])): ?>
                                                <div class="text-xs text-red-600 mb-2 p-2 bg-red-100 border-l-2 border-red-400 rounded">
                                                    <i class="fas fa-search mr-1"></i>
                                                    <strong>Найденная фраза:</strong> 
                                                    <span class="font-mono bg-red-200 px-1 py-0.5 rounded text-red-900"><?= Security::sanitizeOutput($violation['found_text']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($violation['consequence']) && !empty($violation['consequence'])): ?>
                                                <div class="text-xs text-red-600 mb-2">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    <strong>Возможные последствия:</strong> <?= Security::sanitizeOutput($violation['consequence']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (isset($violation['fix']) && !empty($violation['fix'])): ?>
                                                <div class="text-sm text-blue-700 bg-blue-50 p-2 rounded">
                                                    <i class="fas fa-tools mr-1"></i>
                                                    <strong>Как исправить:</strong> <?= Security::sanitizeOutput($violation['fix']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Отсутствующие разделы (только для ИИ) -->
                        <?php if (isset($analysisResult['missing_sections']) && !empty($analysisResult['missing_sections'])): ?>
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    <i class="fas fa-puzzle-piece text-orange-500 mr-2"></i>
                                    Отсутствующие разделы (<?= count($analysisResult['missing_sections']) ?>)
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <?php foreach ($analysisResult['missing_sections'] as $section): ?>
                                        <div class="flex items-center p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                            <i class="fas fa-plus text-orange-500 mr-3"></i>
                                            <span class="text-orange-800 font-medium"><?= Security::sanitizeOutput($section) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Предложения по улучшению -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
                                Предложения по улучшению (<?= count($analysisResult['suggestions']) ?>)
                            </h3>
                            <?php if (empty($analysisResult['suggestions'])): ?>
                                <p class="text-green-600"><i class="fas fa-check-circle mr-2"></i>Документ выглядит достаточно полным</p>
                            <?php else: ?>
                                <div class="space-y-3">
                                    <?php foreach ($analysisResult['suggestions'] as $suggestion): ?>
                                        <div class="p-3 border-l-4 border-blue-400 bg-blue-50">
                                            <div class="text-sm text-blue-700"><?= Security::sanitizeOutput($suggestion['description']) ?></div>
                                            <div class="text-xs text-blue-600 mt-1">Приоритет: <?= Security::sanitizeOutput($suggestion['priority']) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Действия -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Действия с результатом</h3>
                            <div class="flex flex-wrap gap-3">
                                <button onclick="downloadReport()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    <i class="fas fa-download mr-2"></i>Скачать отчет
                                </button>
                                <button onclick="copyReport()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <i class="fas fa-copy mr-2"></i>Копировать результат
                                </button>
                                <a href="documents.php" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                    <i class="fas fa-folder mr-2"></i>Мои документы
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <div class="mb-6">
                            <i class="fas fa-robot text-6xl text-purple-300 mb-4"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">ИИ-анализ юридических документов</h3>
                        <p class="text-gray-500 mb-4">Загрузите документ, чтобы получить профессиональный анализ с помощью GPT-4o</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left max-w-2xl mx-auto">
                            <div class="space-y-3">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Глубокий анализ рисков
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Соответствие российскому праву
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Умные рекомендации
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Анализ критических проблем
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Оценка полноты документа
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Поддержка DOCX, TXT
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Обработка выбора и загрузки файлов
        const fileInput = document.getElementById('document');
        const dropZone = document.getElementById('file-drop-zone');
        const defaultView = document.getElementById('file-upload-default');
        const selectedView = document.getElementById('file-selected');
        const fileNameDisplay = document.getElementById('selected-file-name');
        const fileSizeDisplay = document.getElementById('selected-file-size');
        const changeFileBtn = document.getElementById('change-file');
        
        // Drag & Drop функциональность
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight(e) {
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
        }
        
        function unhighlight(e) {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        }
        
        dropZone.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                showSelectedFile(files[0]);
            }
        }
        
        // Обработка выбора файла
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                showSelectedFile(e.target.files[0]);
            }
        });
        
        // Показать выбранный файл
        function showSelectedFile(file) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            
            if (file.size > maxSize) {
                alert('Файл слишком большой. Максимальный размер: 10MB');
                fileInput.value = '';
                return;
            }
            
            fileNameDisplay.textContent = file.name;
            fileSizeDisplay.textContent = formatFileSize(file.size);
            
            defaultView.classList.add('hidden');
            selectedView.classList.remove('hidden');
        }
        
        // Кнопка изменить файл
        changeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            selectedView.classList.add('hidden');
            defaultView.classList.remove('hidden');
            fileInput.click();
        });
        
        // Форматирование размера файла
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Обработка переключения ИИ
        const useAICheckbox = document.getElementById('use_ai');
        const submitButton = document.querySelector('button[name="analyze_document"]');
        const aiInfoBlock = document.getElementById('ai-info-block');
        const aiFeaturesList = document.querySelector('.mt-6.p-4.bg-gradient-to-r.from-blue-50');
        
        function updateInterface() {
            if (!useAICheckbox || !submitButton) return;
            
            if (useAICheckbox.checked) {
                submitButton.innerHTML = '<i class="fas fa-brain mr-2"></i>Проанализировать с ИИ';
                submitButton.className = 'w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200';
                if (aiInfoBlock) aiInfoBlock.style.display = 'block';
                if (aiFeaturesList) {
                    aiFeaturesList.querySelector('h3').innerHTML = '<i class="fas fa-robot text-purple-600 mr-2"></i>ИИ-анализ включает:';
                }
            } else {
                submitButton.innerHTML = '<i class="fas fa-search mr-2"></i>Базовый анализ';
                submitButton.className = 'w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200';
                if (aiInfoBlock) aiInfoBlock.style.display = 'block'; // Показываем блок всегда
                if (aiFeaturesList) {
                    aiFeaturesList.querySelector('h3').innerHTML = '<i class="fas fa-cogs text-gray-600 mr-2"></i>Базовый анализ включает:';
                }
            }
        }
        
        if (useAICheckbox) {
            useAICheckbox.addEventListener('change', updateInterface);
        }
        
        // Инициализация интерфейса
        updateInterface();

        // Функции для работы с результатами
        function downloadReport() {
            <?php if ($analysisResult): ?>
                const reportData = <?= json_encode($analysisResult, JSON_UNESCAPED_UNICODE) ?>;
                const reportText = generateReportText(reportData);
                
                const blob = new Blob([reportText], { type: 'text/plain;charset=utf-8' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = 'legal_analysis_report.txt';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            <?php endif; ?>
        }
        
        function copyReport() {
            <?php if ($analysisResult): ?>
                const reportData = <?= json_encode($analysisResult, JSON_UNESCAPED_UNICODE) ?>;
                const reportText = generateReportText(reportData);
                
                navigator.clipboard.writeText(reportText).then(function() {
                    alert('Отчет скопирован в буфер обмена');
                });
            <?php endif; ?>
        }
        
        function generateReportText(data) {
            let report = "ОТЧЕТ О ЮРИДИЧЕСКОМ АНАЛИЗЕ ДОКУМЕНТА\n";
            report += "=" + "=".repeat(50) + "\n\n";
            
            report += "ОБЩАЯ ИНФОРМАЦИЯ:\n";
            report += `Полнота документа: ${data.completeness_score || 0}%\n`;
            report += `Тип анализа: ${data.ai_powered ? 'ИИ-анализ (GPT-4o)' : 'Базовый анализ'}\n`;
            
            if (data.overall_assessment) {
                report += `Общая оценка: ${data.overall_assessment}\n`;
            }
            
            const risks = data.risks || [];
            const violations = data.legal_violations || data.violations || [];
            const suggestions = data.suggestions || [];
            
            report += `Найдено проблем: ${risks.length + violations.length}\n\n`;
            
            if (risks.length > 0) {
                report += "ЮРИДИЧЕСКИЕ РИСКИ:\n";
                risks.forEach((risk, index) => {
                    const title = risk.title || risk.type || 'Риск';
                    const desc = risk.description || '';
                    const severity = risk.severity ? ` (${risk.severity})` : '';
                    report += `${index + 1}. ${title}${severity}: ${desc}\n`;
                    if (risk.recommendation) {
                        report += `   Рекомендация: ${risk.recommendation}\n`;
                    }
                });
                report += "\n";
            }
            
            if (violations.length > 0) {
                report += "ВОЗМОЖНЫЕ НАРУШЕНИЯ ЗАКОНОДАТЕЛЬСТВА:\n";
                violations.forEach((violation, index) => {
                    const law = violation.law || violation.area || 'Нарушение';
                    const desc = violation.violation || violation.description || '';
                    report += `${index + 1}. ${law}: ${desc}\n`;
                    if (violation.fix) {
                        report += `   Как исправить: ${violation.fix}\n`;
                    }
                });
                report += "\n";
            }
            
            if (suggestions.length > 0) {
                report += "ПРЕДЛОЖЕНИЯ ПО УЛУЧШЕНИЮ:\n";
                suggestions.forEach((suggestion, index) => {
                    const title = suggestion.title || suggestion.description || 'Предложение';
                    const priority = suggestion.priority ? ` (${suggestion.priority})` : '';
                    report += `${index + 1}. ${title}${priority}\n`;
                    if (suggestion.implementation) {
                        report += `   Как реализовать: ${suggestion.implementation}\n`;
                    }
                });
                report += "\n";
            }
            
            if (data.critical_issues && data.critical_issues.length > 0) {
                report += "КРИТИЧЕСКИЕ ПРОБЛЕМЫ:\n";
                data.critical_issues.forEach((issue, index) => {
                    report += `${index + 1}. ${issue}\n`;
                });
                report += "\n";
            }
            
            if (data.key_strengths && data.key_strengths.length > 0) {
                report += "СИЛЬНЫЕ СТОРОНЫ ДОКУМЕНТА:\n";
                data.key_strengths.forEach((strength, index) => {
                    report += `${index + 1}. ${strength}\n`;
                });
            }
            
            return report;
        }
    </script>
</body>
</html> 