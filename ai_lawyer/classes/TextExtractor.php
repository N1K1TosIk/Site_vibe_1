<?php
/**
 * TextExtractor - Simple text file extraction
 * 
 * This extractor handles plain text files with various encodings.
 * 
 * @author AI Lawyer System
 * @version 2.0
 */

namespace AILawyer\Classes;

use Exception;

class TextExtractor implements DocumentExtractorInterface
{
    private $logger;
    private $supportedEncodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'];
    
    public function __construct()
    {
        $this->logger = new Logger();
    }
    
    /**
     * Extract text from plain text file
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
            $this->logger->info("Starting text file extraction", ['file' => $filePath]);
            
            // Read file content
            $content = $this->readFileContent($filePath);
            
            // Detect and convert encoding
            $text = $this->detectAndConvertEncoding($content);
            
            // Extract metadata
            $metadata = $this->extractMetadata($filePath, $content);
            
            // Calculate statistics
            $statistics = $this->calculateStatistics($text);
            
            // Set results
            $result->setContent($text)
                   ->setMetadata($metadata)
                   ->setStatistics($statistics)
                   ->setProcessingTime(microtime(true) - $startTime);
            
            $this->logger->info("Text file extraction completed successfully", [
                'file' => $filePath,
                'encoding' => $metadata['encoding'],
                'words' => $statistics['word_count']
            ]);
            
        } catch (Exception $e) {
            $result->addError("Text file extraction failed: " . $e->getMessage());
            $this->logger->error("Text file extraction failed", [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
        
        return $result;
    }
    
    /**
     * Read file content
     * 
     * @param string $filePath
     * @return string
     * @throws Exception
     */
    private function readFileContent(string $filePath): string
    {
        $content = file_get_contents($filePath);
        
        if ($content === false) {
            throw new Exception("Failed to read file: {$filePath}");
        }
        
        return $content;
    }
    
    /**
     * Detect and convert encoding
     * 
     * @param string $content
     * @return string
     */
    private function detectAndConvertEncoding(string $content): string
    {
        // Detect encoding
        $encoding = mb_detect_encoding($content, $this->supportedEncodings, true);
        
        if (!$encoding) {
            $encoding = 'UTF-8';
        }
        
        // Convert to UTF-8 if needed
        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        
        // Clean text
        return $this->cleanText($content);
    }
    
    /**
     * Clean and normalize text
     * 
     * @param string $text
     * @return string
     */
    private function cleanText(string $text): string
    {
        // Remove BOM if present
        $text = str_replace("\xEF\xBB\xBF", '', $text);
        
        // Remove control characters except newlines and tabs
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Remove excessive whitespace but preserve paragraph breaks
        $text = preg_replace('/[ \t]+/', ' ', $text); // Убираем множественные пробелы и табы
        $text = preg_replace('/\n[ \t]+/', "\n", $text); // Убираем пробелы в начале строк
        
        // Сохраняем параграфы, но убираем лишние пустые строки
        $text = preg_replace('/\n{3,}/', "\n\n", $text); // Максимум 2 пустые строки подряд
        
        return trim($text);
    }
    
    /**
     * Extract file metadata
     * 
     * @param string $filePath
     * @param string $content
     * @return array
     */
    private function extractMetadata(string $filePath, string $content): array
    {
        $fileInfo = pathinfo($filePath);
        $encoding = mb_detect_encoding($content, $this->supportedEncodings, true) ?: 'UTF-8';
        
        return [
            'filename' => $fileInfo['basename'],
            'extension' => $fileInfo['extension'] ?? '',
            'encoding' => $encoding,
            'file_size' => filesize($filePath),
            'created' => date('Y-m-d H:i:s', filectime($filePath)),
            'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
            'line_count' => substr_count($content, "\n") + 1
        ];
    }
    
    /**
     * Calculate document statistics
     * 
     * @param string $text
     * @return array
     */
    private function calculateStatistics(string $text): array
    {
        return [
            'word_count' => str_word_count($text),
            'character_count' => strlen($text),
            'line_count' => substr_count($text, "\n") + 1,
            'paragraph_count' => count(array_filter(explode("\n\n", $text))),
            'average_words_per_line' => $this->calculateAverageWordsPerLine($text)
        ];
    }
    
    /**
     * Calculate average words per line
     * 
     * @param string $text
     * @return float
     */
    private function calculateAverageWordsPerLine(string $text): float
    {
        $lines = explode("\n", $text);
        $lines = array_filter($lines, function($line) {
            return !empty(trim($line));
        });
        
        if (empty($lines)) {
            return 0;
        }
        
        $totalWords = 0;
        foreach ($lines as $line) {
            $totalWords += str_word_count($line);
        }
        
        return round($totalWords / count($lines), 2);
    }
    
    /**
     * Get supported formats
     * 
     * @return array
     */
    public function getSupportedFormats(): array
    {
        return ['txt'];
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
        return $extension === 'txt';
    }
    
    /**
     * Get extractor metadata
     * 
     * @return array
     */
    public function getMetadata(): array
    {
        return [
            'name' => 'TextExtractor',
            'version' => '2.0',
            'library' => 'Native PHP',
            'description' => 'Simple text file extraction with encoding detection',
            'supported_features' => ['text_extraction', 'encoding_detection', 'metadata_extraction']
        ];
    }
} 