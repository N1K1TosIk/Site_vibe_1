<?php
/**
 * WordExtractor - Modern Word document text extraction using advanced approach
 * 
 * This extractor provides high-quality text extraction from Word documents
 * including DOCX and DOC formats with support for formatting and metadata.
 * Uses modern approach for table extraction.
 * 
 * @author AI Lawyer System
 * @version 3.0
 */

namespace AILawyer\Classes;

use Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;

class WordExtractor implements DocumentExtractorInterface
{
    private $logger;
    
    public function __construct()
    {
        $this->logger = new Logger();
        $this->initializeSettings();
    }
    
    /**
     * Initialize PhpWord settings
     */
    private function initializeSettings(): void
    {
        try {
            // Set temporary directory
            Settings::setTempDir(sys_get_temp_dir());
            
            // Enable ZIP extension for DOCX processing
            Settings::setZipClass(Settings::PCLZIP);
            
        } catch (Exception $e) {
            $this->logger->error("Failed to initialize Word extractor settings", ['error' => $e->getMessage()]);
            throw $e;
        }
    }
    
    /**
     * Extract text from Word document
     * 
     * @param string $filePath
     * @param array $options
     * @return DocumentResult
     * @throws Exception
     */
    public function extract(string $filePath, array $options = []): DocumentResult
    {
        $startTime = microtime(true);
        $result = new DocumentResult();
        
        try {
            $this->logger->info("Starting Word document extraction", ['file' => $filePath]);
            
            // Load document
            $phpWord = IOFactory::load($filePath);
            
            // Extract text content
            $text = $this->extractText($phpWord, $options);
            
            // Extract metadata
            $metadata = $this->extractMetadata($phpWord, $filePath);
            
            // Calculate statistics
            $statistics = $this->calculateStatistics($phpWord, $text);
            
            // Set results
            $result->setContent($text)
                   ->setMetadata($metadata)
                   ->setStatistics($statistics)
                   ->setProcessingTime(microtime(true) - $startTime);
            
            $this->logger->info("Word document extraction completed successfully", [
                'file' => $filePath,
                'sections' => $statistics['section_count'],
                'words' => $statistics['word_count']
            ]);
            
        } catch (Exception $e) {
            $result->addError("Word document extraction failed: " . $e->getMessage());
            $this->logger->error("Word document extraction failed", [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
        
        return $result;
    }
    
    /**
     * Extract text content from Word document
     * 
     * @param PhpWord $phpWord
     * @param array $options
     * @return string
     */
    private function extractText(PhpWord $phpWord, array $options): string
    {
        $text = '';
        $sections = $phpWord->getSections();
        
        foreach ($sections as $sectionIndex => $section) {
            try {
                $sectionText = $this->extractSectionText($section);
                
                if (!empty(trim($sectionText))) {
                    $text .= $sectionText . "\n\n";
                }
                
            } catch (Exception $e) {
                $this->logger->warning("Failed to extract text from section", [
                    'section' => $sectionIndex + 1,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $this->cleanText($text);
    }
    
    /**
     * Extract text from a section
     * 
     * @param object $section
     * @return string
     */
    private function extractSectionText($section): string
    {
        $text = '';
        $elements = $section->getElements();
        
        foreach ($elements as $element) {
            $elementText = $this->extractElementText($element);
            
            // Добавляем двойной перенос строки для разделения параграфов
            if (!empty(trim($elementText))) {
                $text .= $elementText . "\n\n";
            }
        }
        
        return $text;
    }
    
    /**
     * Extract text from an element
     * 
     * @param object $element
     * @return string
     */
    private function extractElementText($element): string
    {
        $text = '';
        
        if (method_exists($element, 'getText')) {
            $text .= $element->getText();
        } elseif (method_exists($element, 'getElements')) {
            $subElements = $element->getElements();
            foreach ($subElements as $subElement) {
                $text .= $this->extractElementText($subElement);
            }
        } elseif (method_exists($element, 'getRows')) {
            // Это таблица - используем новый подход
            $text .= $this->extractTableTextNew($element);
        } elseif (method_exists($element, 'getTextRun')) {
            // Обработка текстовых элементов
            $text .= $element->getTextRun();
        } elseif (method_exists($element, 'getTexts')) {
            // Обработка множественных текстовых элементов
            $texts = $element->getTexts();
            foreach ($texts as $textElement) {
                $text .= $this->extractElementText($textElement);
            }
        }
        
        // Добавляем перенос строки после каждого элемента
        if (!empty(trim($text))) {
            $text .= "\n";
        }
        
        return $text;
    }
    
    /**
     * Extract text from table using new approach
     * 
     * @param object $table
     * @return string
     */
    private function extractTableTextNew($table): string
    {
        $tableData = [];
        $tableText = '';
        $rows = $table->getRows();
        
        // Определяем максимальное количество колонок
        $maxColumns = 0;
        foreach ($rows as $row) {
            $maxColumns = max($maxColumns, count($row->getCells()));
        }
        
        // Обрабатываем каждую строку
        foreach ($rows as $rowIndex => $row) {
            $cells = $row->getCells();
            $rowData = [];
            $rowText = '';
            
            // Обрабатываем все ячейки в строке
            for ($colIndex = 0; $colIndex < $maxColumns; $colIndex++) {
                if (isset($cells[$colIndex])) {
                    $cell = $cells[$colIndex];
                    $cellText = $this->extractCellText($cell);
                    $cellStyle = $this->extractCellStyleNew($cell);
                    
                    // Добавляем текст ячейки в обычный текст
                    $cleanCellText = trim($cellText);
                    if (!empty($cleanCellText)) {
                        $rowText .= $cleanCellText . ' ';
                    }
                    
                    $rowData[] = [
                        'text' => $cleanCellText,
                        'style' => $cellStyle,
                        'row' => $rowIndex,
                        'col' => $colIndex,
                        'isEmpty' => empty($cleanCellText)
                    ];
                } else {
                    // Пустая ячейка
                    $rowData[] = [
                        'text' => '',
                        'style' => [],
                        'row' => $rowIndex,
                        'col' => $colIndex,
                        'isEmpty' => true
                    ];
                }
            }
            
            $tableData[] = $rowData;
            $tableText .= trim($rowText) . "\n";
        }
        
        // Сериализуем данные таблицы
        $text = "TABLE_START\n";
        $text .= "TABLE_DATA:" . json_encode($tableData) . "\n";
        $text .= "TABLE_END\n";
        
        // Добавляем обычный текст таблицы для статистики
        $text .= "\n" . trim($tableText) . "\n";
        
        return $text;
    }
    
    /**
     * Extract text from cell
     * 
     * @param object $cell
     * @return string
     */
    private function extractCellText($cell): string
    {
        $text = '';
        
        if (method_exists($cell, 'getText')) {
            $text .= $cell->getText();
        } elseif (method_exists($cell, 'getElements')) {
            $elements = $cell->getElements();
            foreach ($elements as $element) {
                $text .= $this->extractElementText($element);
            }
        }
        
        return $text;
    }
    
    /**
     * Extract cell style using new approach
     * 
     * @param object $cell
     * @return array
     */
    private function extractCellStyleNew($cell): array
    {
        $style = [];
        
        try {
            // Получаем стили ячейки
            if (method_exists($cell, 'getStyle')) {
                $cellStyle = $cell->getStyle();
                if ($cellStyle) {
                    $style['width'] = $cellStyle->getWidth() ?? null;
                    $style['height'] = $cellStyle->getHeight() ?? null;
                    $style['borderSize'] = $cellStyle->getBorderSize() ?? null;
                    $style['borderColor'] = $cellStyle->getBorderColor() ?? null;
                    $style['backgroundColor'] = $cellStyle->getBgColor() ?? null;
                    $style['verticalAlign'] = $cellStyle->getVAlign() ?? null;
                }
            }
            
            // Получаем стили параграфа
            if (method_exists($cell, 'getParagraphStyle')) {
                $paragraphStyle = $cell->getParagraphStyle();
                if ($paragraphStyle) {
                    $style['alignment'] = $paragraphStyle->getAlignment() ?? null;
                    $style['spaceBefore'] = $paragraphStyle->getSpaceBefore() ?? null;
                    $style['spaceAfter'] = $paragraphStyle->getSpaceAfter() ?? null;
                }
            }
            
        } catch (Exception $e) {
            $this->logger->warning("Failed to extract cell style", ['error' => $e->getMessage()]);
        }
        
        return $style;
    }
    
    /**
     * Clean and normalize text
     * 
     * @param string $text
     * @return string
     */
    private function cleanText(string $text): string
    {
        // Remove control characters except newlines and tabs
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Remove excessive whitespace but preserve paragraph breaks
        $text = preg_replace('/[ \t]+/', ' ', $text); // Убираем множественные пробелы и табы
        $text = preg_replace('/\n[ \t]+/', "\n", $text); // Убираем пробелы в начале строк
        
        // Сохраняем параграфы, но убираем лишние пустые строки
        $text = preg_replace('/\n{3,}/', "\n\n", $text); // Максимум 2 пустые строки подряд
        
        // Remove special Word characters and HTML entities
        $text = str_replace(['\u2013', '\u2014', '\u2018', '\u2019', '\u201C', '\u201D'], 
                           ['-', '-', "'", "'", '"', '"'], $text);
        
        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return trim($text);
    }
    
    /**
     * Extract document metadata
     * 
     * @param PhpWord $phpWord
     * @param string $filePath
     * @return array
     */
    private function extractMetadata(PhpWord $phpWord, string $filePath): array
    {
        $metadata = [];
        
        try {
            $docInfo = $phpWord->getDocInfo();
            
            $metadata = [
                'title' => $docInfo->getTitle() ?? '',
                'subject' => $docInfo->getSubject() ?? '',
                'creator' => $docInfo->getCreator() ?? '',
                'keywords' => $docInfo->getKeywords() ?? '',
                'description' => $docInfo->getDescription() ?? '',
                'category' => $docInfo->getCategory() ?? '',
                'company' => $docInfo->getCompany() ?? '',
                'manager' => $docInfo->getManager() ?? '',
                'created' => $this->formatDate($docInfo->getCreated()),
                'modified' => $this->formatDate($docInfo->getModified()),
                'file_size' => filesize($filePath),
                'section_count' => count($phpWord->getSections())
            ];
            
        } catch (Exception $e) {
            $this->logger->warning("Failed to extract Word document metadata", ['error' => $e->getMessage()]);
        }
        
        return $metadata;
    }
    
    /**
     * Safely format date from various formats
     * 
     * @param mixed $date
     * @return string
     */
    private function formatDate($date): string
    {
        if (empty($date)) {
            return '';
        }
        
        try {
            if ($date instanceof \DateTime) {
                return $date->format('Y-m-d H:i:s');
            } elseif (is_numeric($date)) {
                // Handle timestamp
                return date('Y-m-d H:i:s', (int)$date);
            } elseif (is_string($date)) {
                // Try to parse string date
                $dateObj = new \DateTime($date);
                return $dateObj->format('Y-m-d H:i:s');
            }
        } catch (Exception $e) {
            $this->logger->warning("Failed to format date", ['date' => $date, 'error' => $e->getMessage()]);
        }
        
        return '';
    }
    
    /**
     * Calculate document statistics
     * 
     * @param PhpWord $phpWord
     * @param string $text
     * @return array
     */
    private function calculateStatistics(PhpWord $phpWord, string $text): array
    {
        $sections = $phpWord->getSections();
        
        // Подсчитываем слова правильно для русского текста
        $wordCount = $this->countWords($text);
        
        return [
            'section_count' => count($sections),
            'word_count' => $wordCount,
            'character_count' => strlen($text),
            'character_count_no_spaces' => strlen(preg_replace('/\s+/', '', $text)),
            'line_count' => substr_count($text, "\n") + 1,
            'paragraph_count' => substr_count($text, "\n\n") + 1,
            'average_words_per_section' => count($sections) > 0 ? round($wordCount / count($sections), 2) : 0
        ];
    }
    
    /**
     * Count words in text (supports Russian)
     * 
     * @param string $text
     * @return int
     */
    private function countWords(string $text): int
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
    
    /**
     * Get supported formats
     * 
     * @return array
     */
    public function getSupportedFormats(): array
    {
        return ['docx', 'doc'];
    }
    
    /**
     * Check if this extractor can handle the given file
     * 
     * @param string $filePath
     * @return bool
     */
    public function canHandle(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return in_array($extension, ['docx', 'doc']);
    }
    
    /**
     * Get extractor metadata
     * 
     * @return array
     */
    public function getMetadata(): array
    {
        return [
            'name' => 'WordExtractor',
            'version' => '3.0',
            'library' => 'PhpOffice\PhpWord',
            'description' => 'Advanced Word document text extraction with modern table handling',
            'supported_features' => ['text_extraction', 'metadata_extraction', 'formatting_preservation', 'advanced_table_extraction']
        ];
    }
} 