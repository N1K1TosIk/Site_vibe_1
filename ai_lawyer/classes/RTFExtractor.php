<?php
/**
 * RTFExtractor - RTF document text extraction
 * 
 * This extractor handles Rich Text Format (RTF) documents.
 * 
 * @author AI Lawyer System
 * @version 2.0
 */

namespace AILawyer\Classes;

use Exception;

class RTFExtractor implements DocumentExtractorInterface
{
    private $logger;
    
    public function __construct()
    {
        $this->logger = new Logger();
    }
    
    /**
     * Extract text from RTF document
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
            $this->logger->info("Starting RTF extraction", ['file' => $filePath]);
            
            // Read file content
            $content = $this->readFileContent($filePath);
            
            // Extract text from RTF
            $text = $this->extractText($content);
            
            // Extract metadata
            $metadata = $this->extractMetadata($filePath, $content);
            
            // Calculate statistics
            $statistics = $this->calculateStatistics($text);
            
            // Set results
            $result->setContent($text)
                   ->setMetadata($metadata)
                   ->setStatistics($statistics)
                   ->setProcessingTime(microtime(true) - $startTime);
            
            $this->logger->info("RTF extraction completed successfully", [
                'file' => $filePath,
                'words' => $statistics['word_count']
            ]);
            
        } catch (Exception $e) {
            $result->addError("RTF extraction failed: " . $e->getMessage());
            $this->logger->error("RTF extraction failed", [
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
     * Extract text from RTF content
     * 
     * @param string $content
     * @return string
     */
    private function extractText(string $content): string
    {
        // Check if it's a valid RTF file
        if (strpos($content, '{\rtf') === false) {
            throw new Exception("Invalid RTF file format");
        }
        
        // Remove RTF control words and extract plain text
        $text = $this->parseRTF($content);
        
        // Clean and normalize text
        return $this->cleanText($text);
    }
    
    /**
     * Parse RTF content and extract text
     * 
     * @param string $content
     * @return string
     */
    private function parseRTF(string $content): string
    {
        $text = '';
        $length = strlen($content);
        $i = 0;
        $inGroup = 0;
        $inControl = false;
        $controlWord = '';
        $inText = false;
        
        while ($i < $length) {
            $char = $content[$i];
            
            if ($char === '{') {
                $inGroup++;
                $i++;
                continue;
            }
            
            if ($char === '}') {
                $inGroup--;
                $i++;
                continue;
            }
            
            if ($char === '\\') {
                $inControl = true;
                $controlWord = '';
                $i++;
                continue;
            }
            
            if ($inControl) {
                if (ctype_alpha($char)) {
                    $controlWord .= $char;
                    $i++;
                    continue;
                } else {
                    $inControl = false;
                    
                    // Handle special control words
                    if ($controlWord === 'par') {
                        $text .= "\n";
                    } elseif ($controlWord === 'line') {
                        $text .= "\n";
                    } elseif ($controlWord === 'tab') {
                        $text .= "\t";
                    } elseif ($controlWord === 'emdash') {
                        $text .= '-';
                    } elseif ($controlWord === 'endash') {
                        $text .= '-';
                    } elseif ($controlWord === 'lquote') {
                        $text .= "'";
                    } elseif ($controlWord === 'rquote') {
                        $text .= "'";
                    } elseif ($controlWord === 'ldblquote') {
                        $text .= '"';
                    } elseif ($controlWord === 'rdblquote') {
                        $text .= '"';
                    }
                    
                    // Skip parameter if present
                    if ($char === ' ') {
                        $i++;
                        while ($i < $length && is_numeric($content[$i])) {
                            $i++;
                        }
                    }
                    continue;
                }
            }
            
            // Regular text
            if ($char !== ' ' || $inText) {
                $text .= $char;
                $inText = true;
            }
            
            $i++;
        }
        
        return $text;
    }
    
    /**
     * Clean and normalize extracted text
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
        
        // Extract RTF metadata
        $metadata = [
            'filename' => $fileInfo['basename'],
            'extension' => $fileInfo['extension'] ?? '',
            'file_size' => filesize($filePath),
            'created' => date('Y-m-d H:i:s', filectime($filePath)),
            'modified' => date('Y-m-d H:i:s', filemtime($filePath))
        ];
        
        // Try to extract RTF-specific metadata
        if (preg_match('/\\\title\s*([^}]+)/', $content, $matches)) {
            $metadata['title'] = trim($matches[1]);
        }
        
        if (preg_match('/\\\author\s*([^}]+)/', $content, $matches)) {
            $metadata['author'] = trim($matches[1]);
        }
        
        if (preg_match('/\\\subject\s*([^}]+)/', $content, $matches)) {
            $metadata['subject'] = trim($matches[1]);
        }
        
        return $metadata;
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
            'paragraph_count' => count(array_filter(explode("\n\n", $text)))
        ];
    }
    
    /**
     * Get supported formats
     * 
     * @return array
     */
    public function getSupportedFormats(): array
    {
        return ['rtf'];
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
        return $extension === 'rtf';
    }
    
    /**
     * Get extractor metadata
     * 
     * @return array
     */
    public function getMetadata(): array
    {
        return [
            'name' => 'RTFExtractor',
            'version' => '2.0',
            'library' => 'Native PHP',
            'description' => 'RTF document text extraction with formatting support',
            'supported_features' => ['text_extraction', 'metadata_extraction', 'formatting_handling']
        ];
    }
} 