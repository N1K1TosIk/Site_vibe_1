<?php
/**
 * DocumentProcessor - Modern Document Processing System
 * 
 * This class provides a unified interface for processing various document formats
 * using industry-standard libraries and best practices.
 * 
 * Supported formats:
 * - DOCX (using PhpOffice\PhpWord)
 * - DOC (using PhpOffice\PhpWord)
 * - TXT (plain text)
 * - RTF (using PhpOffice\PhpWord)
 * 
 * @author AI Lawyer System
 * @version 2.0
 */

namespace AILawyer\Classes;

use Exception;
use InvalidArgumentException;
use RuntimeException;

class DocumentProcessor
{
    private const SUPPORTED_FORMATS = ['docx', 'doc', 'txt', 'rtf'];
    private const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB
    
    private $extractors = [];
    private $logger;
    
    public function __construct()
    {
        $this->initializeExtractors();
        $this->logger = new Logger();
    }
    
    /**
     * Initialize document extractors for different formats
     */
    private function initializeExtractors(): void
    {
        $this->extractors = [
            'docx' => new WordExtractor(),
            'doc' => new WordExtractor(),
            'txt' => new TextExtractor(),
            'rtf' => new RTFExtractor()
        ];
    }
    
    /**
     * Process a document and extract its content
     * 
     * @param string $filePath Path to the document file
     * @param array $options Processing options
     * @return DocumentResult
     * @throws Exception
     */
    public function processDocument(string $filePath, array $options = []): DocumentResult
    {
        try {
            // Validate file
            $this->validateFile($filePath);
            
            // Get file format
            $format = $this->getFileFormat($filePath);
            
            // Get appropriate extractor
            $extractor = $this->getExtractor($format);
            
            // Process document
            $result = $extractor->extract($filePath, $options);
            
            // Log success
            $this->logger->info("Document processed successfully", [
                'file' => $filePath,
                'format' => $format,
                'size' => filesize($filePath),
                'output_format' => $options['output_format'] ?? 'text'
            ]);
            
            return $result;
            
        } catch (Exception $e) {
            $this->logger->error("Document processing failed", [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Process a document and extract its content as HTML for display
     * 
     * @param string $filePath Path to the document file
     * @param array $options Processing options
     * @return DocumentResult
     * @throws Exception
     */
    public function processDocumentAsHTML(string $filePath, array $options = []): DocumentResult
    {
        $format = $this->getFileFormat($filePath);
        if (in_array($format, ['txt', 'rtf'])) {
            // Для TXT/RTF — форматируем текст с правильными переносами строк
            $result = $this->processDocument($filePath, $options);
            $text = $result->getContent();
            
            // Обрабатываем переносы строк
            $text = str_replace(["\r\n", "\r"], "\n", $text);
            $text = preg_replace('/\n\s*\n/', "\n\n", $text); // Убираем лишние пустые строки
            $text = trim($text);
            
            // Форматируем как HTML с правильными переносами
            $html = '<div class="text-content txt-content">';
            $paragraphs = explode("\n\n", $text);
            foreach ($paragraphs as $paragraph) {
                $paragraph = trim($paragraph);
                if (!empty($paragraph)) {
                    $html .= '<p>' . nl2br(htmlspecialchars($paragraph)) . '</p>';
                }
            }
            $html .= '</div>';
            
            $result->setContent($html);
            return $result;
        } elseif (in_array($format, ['docx', 'doc'])) {
            // Для DOCX/DOC — форматируем текст с правильными переносами строк
            $result = $this->processDocument($filePath, $options);
            $text = $result->getContent();
            
            // Обрабатываем переносы строк
            $text = str_replace(["\r\n", "\r"], "\n", $text);
            $text = preg_replace('/\n\s*\n/', "\n\n", $text); // Убираем лишние пустые строки
            $text = trim($text);
            
            // Форматируем как HTML с правильными переносами и структурой
            $html = '<div class="text-content docx-content">';
            $paragraphs = explode("\n\n", $text);
            foreach ($paragraphs as $paragraph) {
                $paragraph = trim($paragraph);
                if (!empty($paragraph)) {
                    // Определяем тип параграфа для правильного форматирования
                    if (preg_match('/^\d+\.\s/', $paragraph)) {
                        // Нумерованный список
                        $html .= '<p class="numbered-item">' . nl2br(htmlspecialchars($paragraph)) . '</p>';
                    } elseif (preg_match('/^\d+\.\d+\.\s/', $paragraph)) {
                        // Подпункт
                        $html .= '<p class="sub-item">' . nl2br(htmlspecialchars($paragraph)) . '</p>';
                    } elseif (preg_match('/^[А-ЯЁ][А-ЯЁ\s]+\.$/', trim($paragraph))) {
                        // Заголовок раздела
                        $html .= '<h3 class="section-title">' . htmlspecialchars($paragraph) . '</h3>';
                    } elseif (preg_match('/^[А-ЯЁ][А-ЯЁ\s]+:$/', trim($paragraph))) {
                        // Заголовок с двоеточием
                        $html .= '<h4 class="subsection-title">' . htmlspecialchars($paragraph) . '</h4>';
                    } elseif (preg_match('/^[А-ЯЁ][А-ЯЁ\s]+\.$/', trim($paragraph))) {
                        // Заголовок раздела (альтернативный паттерн)
                        $html .= '<h3 class="section-title">' . htmlspecialchars($paragraph) . '</h3>';
                    } elseif (preg_match('/^[А-ЯЁ][А-ЯЁ\s]+$/', trim($paragraph)) && strlen(trim($paragraph)) > 5) {
                        // Длинный заголовок без знаков препинания
                        $html .= '<h3 class="section-title">' . htmlspecialchars($paragraph) . '</h3>';
                    } elseif ($this->isTableContent($paragraph)) {
                        // Таблица
                        $html .= $this->formatTableAsHTML($paragraph);
                    } elseif ($this->isCenteredContent($paragraph)) {
                        // Центрированный контент
                        $html .= '<p class="centered-text">' . htmlspecialchars($paragraph) . '</p>';
                    } else {
                        // Обычный параграф с сохранением переносов строк
                        $html .= '<p>' . $this->formatTextWithLineBreaks($paragraph) . '</p>';
                    }
                }
            }
            $html .= '</div>';
            
            $result->setContent($html);
            return $result;
        } else {
            throw new InvalidArgumentException("Unsupported file format for HTML preview: {$format}");
        }
    }
    
    /**
     * Validate file before processing
     * 
     * @param string $filePath
     * @throws InvalidArgumentException
     */
    private function validateFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File does not exist: {$filePath}");
        }
        
        if (!is_readable($filePath)) {
            throw new InvalidArgumentException("File is not readable: {$filePath}");
        }
        
        $fileSize = filesize($filePath);
        if ($fileSize > self::MAX_FILE_SIZE) {
            throw new InvalidArgumentException("File size exceeds maximum limit: {$fileSize} bytes");
        }
        
        if ($fileSize === 0) {
            throw new InvalidArgumentException("File is empty: {$filePath}");
        }
    }
    
    /**
     * Get file format from extension
     * 
     * @param string $filePath
     * @return string
     * @throws InvalidArgumentException
     */
    private function getFileFormat(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        if (!in_array($extension, self::SUPPORTED_FORMATS)) {
            throw new InvalidArgumentException("Unsupported file format: {$extension}");
        }
        
        return $extension;
    }
    
    /**
     * Get appropriate extractor for file format
     * 
     * @param string $format
     * @return DocumentExtractorInterface
     * @throws RuntimeException
     */
    private function getExtractor(string $format): DocumentExtractorInterface
    {
        if (!isset($this->extractors[$format])) {
            throw new InvalidArgumentException("No extractor for format: {$format}");
        }
        
        return $this->extractors[$format];
    }
    
    /**
     * Get supported formats
     * 
     * @return array
     */
    public function getSupportedFormats(): array
    {
        return self::SUPPORTED_FORMATS;
    }
    
    /**
     * Get maximum file size
     * 
     * @return int
     */
    public function getMaxFileSize(): int
    {
        return self::MAX_FILE_SIZE;
    }
    
    /**
     * Check if format is supported
     * 
     * @param string $format
     * @return bool
     */
    public function isFormatSupported(string $format): bool
    {
        return in_array(strtolower($format), self::SUPPORTED_FORMATS);
    }
    
    /**
     * Format text with proper line breaks
     * 
     * @param string $text
     * @return string
     */
    private function formatTextWithLineBreaks(string $text): string
    {
        // Сохраняем оригинальные переносы строк
        $text = htmlspecialchars($text);
        
        // Заменяем одинарные переносы на <br>
        $text = str_replace("\n", "<br>", $text);
        
        // Заменяем двойные переносы на параграфы
        $text = str_replace("<br><br>", "</p><p>", $text);
        
        return $text;
    }
    
    /**
     * Check if content is table-like
     * 
     * @param string $content
     * @return bool
     */
    private function isTableContent(string $content): bool
    {
        // Проверяем маркеры таблицы
        if (strpos($content, 'TABLE_START') !== false && strpos($content, 'TABLE_END') !== false) {
            return true;
        }
        
        // Проверяем наличие табуляции или множественных пробелов для выравнивания
        $lines = explode("\n", $content);
        $hasTabs = false;
        $hasMultipleSpaces = false;
        $hasTableKeywords = false;
        
        // Ключевые слова, указывающие на таблицу
        $tableKeywords = ['Арендодатель:', 'Арендатор:', 'Паспорт:', 'выдан:', 'Зарегистрирован:', 'Подпись'];
        
        foreach ($lines as $line) {
            if (strpos($line, "\t") !== false) {
                $hasTabs = true;
            }
            if (preg_match('/\s{3,}/', $line)) {
                $hasMultipleSpaces = true;
            }
            
            // Проверяем наличие ключевых слов таблицы
            foreach ($tableKeywords as $keyword) {
                if (strpos($line, $keyword) !== false) {
                    $hasTableKeywords = true;
                    break;
                }
            }
        }
        
        return $hasTabs || $hasMultipleSpaces || $hasTableKeywords;
    }
    
    /**
     * Check if content should be centered
     * 
     * @param string $content
     * @return bool
     */
    private function isCenteredContent(string $content): bool
    {
        $content = trim($content);
        
        // Проверяем паттерны для центрированного контента
        $centeredPatterns = [
            '/^[А-ЯЁ][А-ЯЁ\s]+$/', // Заголовки из заглавных букв
            '/^г\.\s*\(/', // Даты в начале документа
            '/^[А-ЯЁ][а-яё\s]+:$/', // Заголовки с двоеточием
            '/^ДОГОВОР\s+АРЕНДЫ\s+КВАРТИРЫ$/', // Конкретный заголовок
            '/^[А-ЯЁ][А-ЯЁ\s]+$/', // Любые заголовки из заглавных букв
        ];
        
        foreach ($centeredPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        // Дополнительная проверка для коротких строк, которые обычно центрируются
        if (strlen($content) < 50 && preg_match('/^[А-ЯЁ]/', $content)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Format table content as HTML with professional structure
     * 
     * @param string $content
     * @return string
     */
    private function formatTableAsHTML(string $content): string
    {
        // Проверяем, есть ли структурированные данные таблицы
        if (strpos($content, 'TABLE_DATA:') !== false) {
            return $this->formatStructuredTable($content);
        }
        
        // Убираем маркеры таблицы
        $content = str_replace(['TABLE_START', 'TABLE_END'], '', $content);
        
        $lines = explode("\n", $content);
        $html = '<div class="table-container">';
        $html .= '<table class="document-table">';
        
        $tableRows = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            if (strpos($line, "\t") !== false) {
                // Разделяем по табуляции
                $cells = explode("\t", $line);
                $tableRows[] = array_map('trim', $cells);
            } else {
                // Разделяем по множественным пробелам
                $cells = preg_split('/\s{3,}/', $line);
                $tableRows[] = array_map('trim', $cells);
            }
        }
        
        // Определяем количество колонок
        $maxColumns = 0;
        foreach ($tableRows as $row) {
            $maxColumns = max($maxColumns, count($row));
        }
        
        // Если это таблица с паспортными данными, форматируем специально
        if ($this->isPassportDataTable($tableRows)) {
            return $this->formatPassportDataTable($tableRows);
        } else {
            // Обычная таблица
            foreach ($tableRows as $row) {
                $html .= '<tr>';
                for ($i = 0; $i < $maxColumns; $i++) {
                    $cell = isset($row[$i]) ? $row[$i] : '';
                    if (!empty(trim($cell))) {
                        $html .= '<td>' . htmlspecialchars($cell) . '</td>';
                    } else {
                        $html .= '<td class="empty-cell">&nbsp;</td>';
                    }
                }
                $html .= '</tr>';
            }
        }
        
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Format structured table with full professional layout
     * 
     * @param string $content
     * @return string
     */
    private function formatStructuredTable(string $content): string
    {
        // Извлекаем данные таблицы
        preg_match('/TABLE_DATA:(.*?)(?=\n|$)/s', $content, $matches);
        if (empty($matches[1])) {
            return '<div class="table-error">Ошибка извлечения данных таблицы</div>';
        }
        
        $tableData = json_decode($matches[1], true);
        if (!$tableData) {
            return '<div class="table-error">Ошибка декодирования данных таблицы</div>';
        }
        
        // Анализируем структуру таблицы
        $tableStructure = $this->analyzeTableStructure($tableData);
        
        // Форматируем в зависимости от типа таблицы
        if ($tableStructure['type'] === 'passport_data') {
            return $this->formatProfessionalPassportTable($tableData, $tableStructure);
        } else {
            return $this->formatProfessionalGenericTable($tableData, $tableStructure);
        }
    }
    
    /**
     * Analyze table structure to determine type and layout
     * 
     * @param array $tableData
     * @return array
     */
    private function analyzeTableStructure(array $tableData): array
    {
        $structure = [
            'type' => 'generic',
            'columns' => 0,
            'rows' => count($tableData),
            'hasPassportData' => false,
            'hasLandlord' => false,
            'hasTenant' => false,
            'columnWidths' => [],
            'cellStyles' => []
        ];
        
        // Определяем количество колонок
        foreach ($tableData as $row) {
            $structure['columns'] = max($structure['columns'], count($row));
        }
        
        // Анализируем содержимое
        foreach ($tableData as $rowIndex => $row) {
            foreach ($row as $colIndex => $cell) {
                $text = $cell['text'] ?? '';
                
                if (strpos($text, 'Арендодатель') !== false) {
                    $structure['hasLandlord'] = true;
                    $structure['hasPassportData'] = true;
                }
                if (strpos($text, 'Арендатор') !== false) {
                    $structure['hasTenant'] = true;
                    $structure['hasPassportData'] = true;
                }
                if (strpos($text, 'Паспорт') !== false) {
                    $structure['hasPassportData'] = true;
                }
                
                // Сохраняем стили ячеек
                $structure['cellStyles'][$rowIndex][$colIndex] = $cell['style'] ?? [];
            }
        }
        
        // Определяем тип таблицы
        if ($structure['hasPassportData'] && $structure['hasLandlord'] && $structure['hasTenant']) {
            $structure['type'] = 'passport_data';
        }
        
        return $structure;
    }
    
    /**
     * Format professional passport data table
     * 
     * @param array $tableData
     * @param array $structure
     * @return string
     */
    private function formatProfessionalPassportTable(array $tableData, array $structure): string
    {
        $html = '<div class="professional-table-container">';
        $html .= '<table class="professional-table passport-table">';
        
        // Используем новый подход - берем только уникальные строки
        $uniqueRows = $this->getUniqueTableRows($tableData);
        
        // Рендерим уникальные строки
        foreach ($uniqueRows as $rowIndex => $row) {
            $html .= '<tr>';
            
            foreach ($row as $colIndex => $cell) {
                $text = $cell['text'] ?? '';
                $style = $cell['style'] ?? [];
                $isEmpty = $cell['isEmpty'] ?? false;
                
                $cssClass = $this->getCellCssClass($text, 'passport');
                $cssStyle = $this->getCellCssStyle($style);
                
                $html .= '<td class="' . $cssClass . '" style="' . $cssStyle . '">';
                
                if ($isEmpty) {
                    $html .= '&nbsp;'; // Пустая ячейка
                } else {
                    $html .= htmlspecialchars($text);
                }
                
                $html .= '</td>';
            }
            
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Get unique table rows (remove duplicates)
     * 
     * @param array $tableData
     * @return array
     */
    private function getUniqueTableRows(array $tableData): array
    {
        $uniqueRows = [];
        $seenContent = [];
        
        foreach ($tableData as $row) {
            $rowContent = '';
            foreach ($row as $cell) {
                $rowContent .= ' ' . ($cell['text'] ?? '');
            }
            $rowContent = trim($rowContent);
            
            // Если такой контент уже видели, пропускаем
            if (!empty($rowContent) && in_array($rowContent, $seenContent)) {
                continue;
            }
            
            $uniqueRows[] = $row;
            if (!empty($rowContent)) {
                $seenContent[] = $rowContent;
            }
        }
        
        return $uniqueRows;
    }
    

    
    /**
     * Extract landlord data from table
     * 
     * @param array $tableData
     * @return array
     */
    private function extractLandlordData(array $tableData): array
    {
        $landlordData = [];
        
        foreach ($tableData as $row) {
            foreach ($row as $cell) {
                $text = $cell['text'] ?? '';
                if (strpos($text, 'Арендодатель') !== false || 
                    (strpos($text, 'Паспорт') !== false && !strpos($text, 'Арендатор')) ||
                    (strpos($text, 'выдан') !== false && !strpos($text, 'Арендатор')) ||
                    (strpos($text, 'Зарегистрирован') !== false && !strpos($text, 'Арендатор')) ||
                    (strpos($text, 'Подпись') !== false && !strpos($text, 'Арендатор'))) {
                    $landlordData[] = $cell;
                }
            }
        }
        
        return $landlordData;
    }
    
    /**
     * Extract tenant data from table
     * 
     * @param array $tableData
     * @return array
     */
    private function extractTenantData(array $tableData): array
    {
        $tenantData = [];
        
        foreach ($tableData as $row) {
            foreach ($row as $cell) {
                $text = $cell['text'] ?? '';
                if (strpos($text, 'Арендатор') !== false || 
                    (strpos($text, 'Паспорт') !== false && strpos($text, 'Арендатор')) ||
                    (strpos($text, 'выдан') !== false && strpos($text, 'Арендатор')) ||
                    (strpos($text, 'Зарегистрирован') !== false && strpos($text, 'Арендатор')) ||
                    (strpos($text, 'Подпись') !== false && strpos($text, 'Арендатор'))) {
                    $tenantData[] = $cell;
                }
            }
        }
        
        return $tenantData;
    }
    
    /**
     * Render table rows with professional styling
     * 
     * @param array $data
     * @param string $type
     * @return string
     */
    private function renderTableRows(array $data, string $type): string
    {
        $html = '';
        
        foreach ($data as $cell) {
            $text = $cell['text'] ?? '';
            $style = $cell['style'] ?? [];
            
            $cssClass = $this->getCellCssClass($text, $type);
            $cssStyle = $this->getCellCssStyle($style);
            
            $html .= '<tr>';
            $html .= '<td class="' . $cssClass . '" style="' . $cssStyle . '">';
            $html .= htmlspecialchars($text);
            $html .= '</td>';
            $html .= '</tr>';
        }
        
        return $html;
    }
    
    /**
     * Get CSS class for cell based on content
     * 
     * @param string $text
     * @param string $type
     * @return string
     */
    private function getCellCssClass(string $text, string $type): string
    {
        $classes = ['table-cell'];
        
        if (empty(trim($text))) {
            $classes[] = 'empty-cell';
        } else {
            if (strpos($text, 'Арендодатель') !== false || strpos($text, 'Арендатор') !== false) {
                $classes[] = 'table-header';
            }
            
            if (strpos($text, 'Паспорт') !== false) {
                $classes[] = 'passport-field';
            }
            
            if (strpos($text, 'выдан') !== false) {
                $classes[] = 'issued-field';
            }
            
            if (strpos($text, 'Зарегистрирован') !== false) {
                $classes[] = 'registered-field';
            }
            
            if (strpos($text, 'Подпись') !== false) {
                $classes[] = 'signature-field';
            }
            
            if (strpos($text, 'серия') !== false || strpos($text, '№') !== false) {
                $classes[] = 'series-number-field';
            }
        }
        
        $classes[] = $type . '-cell';
        
        return implode(' ', $classes);
    }
    
    /**
     * Get CSS style for cell based on extracted style
     * 
     * @param array $style
     * @return string
     */
    private function getCellCssStyle(array $style): string
    {
        $css = [];
        
        if (!empty($style['width'])) {
            $css[] = 'width: ' . $style['width'];
        }
        
        if (!empty($style['height'])) {
            $css[] = 'height: ' . $style['height'];
        }
        
        if (!empty($style['alignment'])) {
            $css[] = 'text-align: ' . $style['alignment'];
        }
        
        if (!empty($style['verticalAlignment'])) {
            $css[] = 'vertical-align: ' . $style['verticalAlignment'];
        }
        
        return implode('; ', $css);
    }
    
    /**
     * Format professional generic table
     * 
     * @param array $tableData
     * @param array $structure
     * @return string
     */
    private function formatProfessionalGenericTable(array $tableData, array $structure): string
    {
        $html = '<div class="professional-table-container">';
        $html .= '<table class="professional-table generic-table">';
        
        foreach ($tableData as $rowIndex => $row) {
            $html .= '<tr>';
            foreach ($row as $colIndex => $cell) {
                $text = $cell['text'] ?? '';
                $style = $cell['style'] ?? [];
                
                $cssStyle = $this->getCellCssStyle($style);
                
                $html .= '<td style="' . $cssStyle . '">';
                $html .= htmlspecialchars($text);
                $html .= '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render landlord section with proper line breaks
     * 
     * @param array $landlordData
     * @return string
     */
    private function renderLandlordSection(array $landlordData): string
    {
        $html = '';
        
        foreach ($landlordData as $cell) {
            $text = $cell['text'] ?? '';
            if (empty(trim($text))) continue;
            
            $html .= '<div class="landlord-field">';
            $html .= htmlspecialchars($text);
            $html .= '</div>';
        }
        
        return $html;
    }
    
    /**
     * Render tenant section with proper line breaks
     * 
     * @param array $tenantData
     * @return string
     */
    private function renderTenantSection(array $tenantData): string
    {
        $html = '';
        
        foreach ($tenantData as $cell) {
            $text = $cell['text'] ?? '';
            if (empty(trim($text))) continue;
            
            $html .= '<div class="tenant-field">';
            $html .= htmlspecialchars($text);
            $html .= '</div>';
        }
        
        return $html;
    }
    
    /**
     * Split table row into cells
     * 
     * @param string $line
     * @return array
     */
    private function splitTableRow(string $line): array
    {
        // Сначала пробуем разделить по множественным пробелам
        $cells = preg_split('/\s{3,}/', $line);
        
        // Если получилось мало ячеек, пробуем другой подход
        if (count($cells) < 2) {
            // Разделяем по ключевым словам
            $keywords = ['Арендодатель:', 'Арендатор:', 'Паспорт:', 'выдан:', 'Зарегистрирован:', 'Подпись'];
            $result = [];
            $currentCell = '';
            
            foreach ($keywords as $keyword) {
                $pos = strpos($line, $keyword);
                if ($pos !== false) {
                    if (!empty($currentCell)) {
                        $result[] = trim($currentCell);
                    }
                    $currentCell = $keyword;
                } else {
                    $currentCell .= ' ' . $keyword;
                }
            }
            
            if (!empty($currentCell)) {
                $result[] = trim($currentCell);
            }
            
            if (!empty($result)) {
                return $result;
            }
        }
        
        return $cells;
    }
    
    /**
     * Check if this is a passport data table
     * 
     * @param array $tableRows
     * @return bool
     */
    private function isPassportDataTable(array $tableRows): bool
    {
        foreach ($tableRows as $row) {
            foreach ($row as $cell) {
                if (strpos($cell, 'Арендодатель:') !== false || 
                    strpos($cell, 'Арендатор:') !== false ||
                    strpos($cell, 'Паспорт:') !== false) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Format passport data table specifically
     * 
     * @param array $tableRows
     * @return string
     */
    private function formatPassportDataTable(array $tableRows): string
    {
        $html = '';
        
        // Создаем структуру для двух отдельных таблиц
        $landlordData = [];
        $tenantData = [];
        
        // Разбираем данные по категориям
        $currentSection = 'landlord'; // Начинаем с арендодателя
        
        foreach ($tableRows as $row) {
            foreach ($row as $cell) {
                $cell = trim($cell);
                if (empty($cell)) continue;
                
                if (strpos($cell, 'Арендодатель:') !== false) {
                    $landlordData['header'] = $cell;
                    $currentSection = 'landlord';
                } elseif (strpos($cell, 'Арендатор:') !== false) {
                    $tenantData['header'] = $cell;
                    $currentSection = 'tenant';
                } elseif (strpos($cell, 'Паспорт:') !== false) {
                    if ($currentSection === 'tenant') {
                        $tenantData['passport'] = $cell;
                    } else {
                        $landlordData['passport'] = $cell;
                    }
                } elseif (strpos($cell, 'выдан:') !== false) {
                    if ($currentSection === 'tenant') {
                        $tenantData['issued'] = $cell;
                    } else {
                        $landlordData['issued'] = $cell;
                    }
                } elseif (strpos($cell, 'Зарегистрирован') !== false) {
                    if ($currentSection === 'tenant') {
                        $tenantData['registered'] = $cell;
                    } else {
                        $landlordData['registered'] = $cell;
                    }
                } elseif (strpos($cell, 'Подпись') !== false) {
                    if ($currentSection === 'tenant') {
                        $tenantData['signature'] = $cell;
                    } else {
                        $landlordData['signature'] = $cell;
                    }
                } else {
                    // Дополнительные строки адреса
                    if ($currentSection === 'tenant') {
                        $tenantData['address2'] = $cell;
                    } else {
                        $landlordData['address2'] = $cell;
                    }
                }
            }
        }
        
        // Создаем контейнер для двух таблиц
        $html .= '<div class="dual-table-container">';
        
        // Таблица Арендодателя
        $html .= '<div class="table-wrapper">';
        $html .= '<table class="document-table passport-table">';
        $html .= '<tr><td class="table-header">' . htmlspecialchars($landlordData['header'] ?? 'Арендодатель:') . '</td></tr>';
        if (isset($landlordData['passport'])) {
            $html .= '<tr><td>' . htmlspecialchars($landlordData['passport']) . '</td></tr>';
        }
        if (isset($landlordData['issued'])) {
            $html .= '<tr><td>' . htmlspecialchars($landlordData['issued']) . '</td></tr>';
        }
        if (isset($landlordData['registered'])) {
            $html .= '<tr><td>' . htmlspecialchars($landlordData['registered']) . '</td></tr>';
        }
        if (isset($landlordData['address2'])) {
            $html .= '<tr><td>' . htmlspecialchars($landlordData['address2']) . '</td></tr>';
        }
        if (isset($landlordData['signature'])) {
            $html .= '<tr><td>' . htmlspecialchars($landlordData['signature']) . '</td></tr>';
        }
        $html .= '</table>';
        $html .= '</div>';
        
        // Таблица Арендатора
        $html .= '<div class="table-wrapper">';
        $html .= '<table class="document-table passport-table">';
        $html .= '<tr><td class="table-header">' . htmlspecialchars($tenantData['header'] ?? 'Арендатор:') . '</td></tr>';
        if (isset($tenantData['passport'])) {
            $html .= '<tr><td>' . htmlspecialchars($tenantData['passport']) . '</td></tr>';
        }
        if (isset($tenantData['issued'])) {
            $html .= '<tr><td>' . htmlspecialchars($tenantData['issued']) . '</td></tr>';
        }
        if (isset($tenantData['registered'])) {
            $html .= '<tr><td>' . htmlspecialchars($tenantData['registered']) . '</td></tr>';
        }
        if (isset($tenantData['address2'])) {
            $html .= '<tr><td>' . htmlspecialchars($tenantData['address2']) . '</td></tr>';
        }
        if (isset($tenantData['signature'])) {
            $html .= '<tr><td>' . htmlspecialchars($tenantData['signature']) . '</td></tr>';
        }
        $html .= '</table>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }
} 