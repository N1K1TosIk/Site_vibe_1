<?php
session_start();
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/ContractGenerator.php';

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
$contractGenerator = new ContractGenerator();

// Обработка действий с документами
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Недействительный токен безопасности';
    } else {
        $action = $_POST['action'] ?? '';
        $documentId = $_POST['document_id'] ?? 0;
        
        switch ($action) {
            case 'delete':
                if (deleteDocument($_SESSION['user_id'], $documentId)) {
                    $success = 'Документ успешно удален';
                } else {
                    $error = 'Ошибка при удалении документа';
                }
                break;
                
            
                
            case 'export_word':
                exportDocumentToWord($_SESSION['user_id'], $documentId);
                break;
        }
    }
}

// Получение списка документов пользователя
$documents = $contractGenerator->getUserDocuments($_SESSION['user_id'], 50);
$statistics = getDocumentStatistics($_SESSION['user_id']);

$csrfToken = Security::generateCSRFToken();

// Функция получения статистики документов
function getDocumentStatistics($userId) {
    try {
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT 
                    COUNT(*) as total_documents,
                    SUM(CASE WHEN document_type = 'generated' THEN 1 ELSE 0 END) as generated_contracts,
                    SUM(CASE WHEN document_type = 'analyzed' THEN 1 ELSE 0 END) as analyzed_documents,
                    COUNT(DISTINCT template_id) as unique_templates
                  FROM user_documents 
                  WHERE user_id = :user_id";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetch();
    } catch (Exception $e) {
        return [
            'total_documents' => 0,
            'generated_contracts' => 0,
            'analyzed_documents' => 0,
            'unique_templates' => 0
        ];
    }
}

// Функция удаления документа
function deleteDocument($userId, $documentId) {
    try {
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        
        // Удаление записи из БД
        $query = "DELETE FROM user_documents WHERE id = :document_id AND user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':document_id', $documentId);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    } catch (Exception $e) {
        return false;
    }
}



// Функция экспорта документа в Word
function exportDocumentToWord($userId, $documentId) {
    global $contractGenerator;
    
    $document = $contractGenerator->getDocumentById($documentId, $userId);
    if (!$document) {
        return false;
    }
    
    $content = $document['document_data']['generated_content'] ?? '';
    $filename = sanitizeFilename($document['title']);
    
    // Создаем RTF документ (Rich Text Format) который откроется в Word
    $rtfContent = '{\\rtf1\\ansi\\deff0 {\\fonttbl {\\f0 Times New Roman;}}';
    $rtfContent .= '\\f0\\fs24 ';
    
    // Заменяем специальные символы для RTF
    $content = str_replace('\\', '\\\\', $content);
    $content = str_replace('{', '\\{', $content);
    $content = str_replace('}', '\\}', $content);
    $content = str_replace("\n", '\\par ', $content);
    
    $rtfContent .= $content;
    $rtfContent .= '}';
    
    // Отправляем как RTF файл
    header('Content-Type: application/rtf');
    header('Content-Disposition: attachment; filename="' . $filename . '.rtf"');
    header('Cache-Control: max-age=0');
    
    echo $rtfContent;
    exit;
}

// Функция очистки имени файла
function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Zа-яА-Я0-9\s\-_]/', '', $filename);
    $filename = preg_replace('/\s+/', '_', $filename);
    return substr($filename, 0, 50);
}

// Функция получения типа документа на русском
function getDocumentTypeName($type) {
    $types = [
        'generated' => 'Сгенерированный договор',
        'uploaded' => 'Загруженный документ',
        'analyzed' => 'Проанализированный документ'
    ];
    return $types[$type] ?? 'Неизвестный тип';
}

// Функция форматирования размера файла
function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' МБ';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' КБ';
    } else {
        return $bytes . ' байт';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои документы - AI Юрист</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            .document-content { font-size: 12pt; line-height: 1.5; }
        }
        
        /* Стили для модального окна */
        #modal-content h1, #modal-content h2, #modal-content h3 {
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        
        #modal-content h3 {
            font-size: 14pt;
        }
        
        #modal-content p {
            margin: 10px 0;
            text-align: justify;
        }
        
        #modal-content table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        #modal-content td {
            padding: 10px;
            vertical-align: top;
        }
        
        #modal-content strong {
            font-weight: bold;
        }
        
        #modal-content div[style*="text-align: center"] {
            text-align: center !important;
        }
        
        #modal-content div[style*="text-align: right"] {
            text-align: right !important;
        }
        
        #modal-content div[style*="border-bottom"] {
            border-bottom: 1px solid black;
            display: inline-block;
            min-width: 150px;
            margin: 10px auto;
        }
        
        #modal-content div[style*="font-size: 16pt"] {
            font-size: 16pt !important;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body class="bg-gray-50 pt-12">
    <?php include '_navbar.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Мои документы</h1>
            <p class="mt-2 text-gray-600">Управление созданными и загруженными документами</p>
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

        <!-- Статистика -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900"><?= $statistics['total_documents'] ?></p>
                        <p class="text-gray-600 text-sm">Всего документов</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-file-contract text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900"><?= $statistics['generated_contracts'] ?></p>
                        <p class="text-gray-600 text-sm">Создано договоров</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-search text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900"><?= $statistics['analyzed_documents'] ?></p>
                        <p class="text-gray-600 text-sm">Анализов проведено</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100">
                        <i class="fas fa-layer-group text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900"><?= $statistics['unique_templates'] ?></p>
                        <p class="text-gray-600 text-sm">Типов шаблонов</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Список документов -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Список документов</h2>
            </div>
            
            <?php if (empty($documents)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">У вас пока нет документов</p>
                    <p class="text-gray-400 text-sm mt-2">Создайте свой первый договор в генераторе</p>
                    <a href="generator.php" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Создать договор
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Документ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата создания</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($documents as $doc): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                    <i class="fas fa-file-contract text-blue-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= Security::sanitizeOutput($doc['title']) ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?= $doc['template_name'] ? Security::sanitizeOutput($doc['template_name']) : 'Документ' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            <?= $doc['document_type'] === 'generated' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                            <?= getDocumentTypeName($doc['document_type']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('d.m.Y H:i', strtotime($doc['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button 
                                                onclick="viewDocument(<?= $doc['id'] ?>)"
                                                class="text-blue-600 hover:text-blue-900 transition duration-200"
                                                title="Просмотр"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <button 
                                                onclick="printDocument(<?= $doc['id'] ?>)"
                                                class="text-green-600 hover:text-green-900 transition duration-200"
                                                title="Печать"
                                            >
                                                <i class="fas fa-print"></i>
                                            </button>
                                            
                                            <div class="relative inline-block text-left">
                                                <button 
                                                    onclick="toggleExportMenu(<?= $doc['id'] ?>)"
                                                    class="text-purple-600 hover:text-purple-900 transition duration-200"
                                                    title="Скачать"
                                                >
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                
                                                <div id="export-menu-<?= $doc['id'] ?>" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                    <div class="py-1">

                                                        <form method="POST" class="inline">
                                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                            <input type="hidden" name="action" value="export_word">
                                                            <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <i class="fas fa-file-word mr-2 text-blue-600"></i>Скачать Word
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <button 
                                                onclick="deleteDocument(<?= $doc['id'] ?>, '<?= Security::sanitizeOutput($doc['title']) ?>')"
                                                class="text-red-600 hover:text-red-900 transition duration-200"
                                                title="Удалить"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Модальное окно просмотра документа -->
    <div id="document-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">Просмотр документа</h3>
                    <div class="flex space-x-2">
                        <button 
                            onclick="printModalContent()"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-200"
                        >
                            <i class="fas fa-print mr-2"></i>Печать
                        </button>
                        <button 
                            onclick="closeModal()"
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition duration-200"
                        >
                            <i class="fas fa-times mr-2"></i>Закрыть
                        </button>
                    </div>
                </div>
                <div id="modal-content" class="bg-white p-8 rounded-lg max-h-96 overflow-y-auto document-content">
                    <!-- Содержимое документа будет загружено здесь -->
                </div>
            </div>
        </div>
    </div>

    <style>
        .document-content { font-family: 'Times New Roman', serif; line-height: 1.6; text-align: justify; }

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

    <!-- Форма для удаления документа -->
    <form id="delete-form" method="POST" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="document_id" id="delete-document-id">
    </form>

    <script>
        // Просмотр документа
        async function viewDocument(documentId) {
            try {
                const response = await fetch('get_document_content.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ document_id: documentId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('modal-title').textContent = data.document.title;
                    document.getElementById('modal-content').innerHTML = data.document.content;
                    document.getElementById('document-modal').classList.remove('hidden');
                } else {
                    alert('Ошибка загрузки документа: ' + data.message);
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при загрузке документа');
            }
        }

        // Закрытие модального окна
        function closeModal() {
            document.getElementById('document-modal').classList.add('hidden');
        }

        // Печать содержимого модального окна
        function printModalContent() {
            const content = document.getElementById('modal-content').innerHTML;
            const title = document.getElementById('modal-title').textContent;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>${title}</title>
                    <style>
                        body { font-family: 'Times New Roman', serif; line-height: 1.6; margin: 40px; }
                        h1, h2, h3 { color: #333; }
                        .signature-line { border-bottom: 1px solid #000; display: inline-block; width: 200px; margin: 0 10px; }
                    </style>
                </head>
                <body>
                    <h1>${title}</h1>
                    <div style="white-space: pre-wrap;">${content}</div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        // Печать документа напрямую
        async function printDocument(documentId) {
            try {
                const response = await fetch('get_document_content.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ document_id: documentId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>${data.document.title}</title>
                            <style>
                                body { font-family: 'Times New Roman', serif; line-height: 1.6; margin: 40px; }
                                h1, h2, h3 { color: #333; }
                                .signature-line { border-bottom: 1px solid #000; display: inline-block; width: 200px; margin: 0 10px; }
                            </style>
                        </head>
                        <body>
                            <h1>${data.document.title}</h1>
                            <div style="white-space: pre-wrap;">${data.document.content}</div>
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                    printWindow.print();
                } else {
                    alert('Ошибка загрузки документа: ' + data.message);
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при загрузке документа');
            }
        }

        // Переключение меню экспорта
        function toggleExportMenu(documentId) {
            const menu = document.getElementById(`export-menu-${documentId}`);
            const isHidden = menu.classList.contains('hidden');
            
            // Скрыть все открытые меню
            document.querySelectorAll('[id^="export-menu-"]').forEach(m => m.classList.add('hidden'));
            
            // Показать текущее меню, если оно было скрыто
            if (isHidden) {
                menu.classList.remove('hidden');
            }
        }

        // Удаление документа
        function deleteDocument(documentId, documentTitle) {
            if (confirm(`Вы уверены, что хотите удалить документ "${documentTitle}"?`)) {
                document.getElementById('delete-document-id').value = documentId;
                document.getElementById('delete-form').submit();
            }
        }

        // Закрытие меню при клике вне их
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[onclick^="toggleExportMenu"]') && !event.target.closest('[id^="export-menu-"]')) {
                document.querySelectorAll('[id^="export-menu-"]').forEach(menu => menu.classList.add('hidden'));
            }
        });

        // Закрытие модального окна при клике вне его
        document.getElementById('document-modal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html> 