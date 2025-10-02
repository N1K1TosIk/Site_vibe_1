<?php
/**
 * DocumentResult - Result container for document processing
 * 
 * This class encapsulates the results of document processing operations,
 * including extracted text, metadata, and processing statistics.
 * 
 * @author AI Lawyer System
 * @version 2.0
 */

namespace AILawyer\Classes;

class DocumentResult
{
    private $content;
    private $metadata;
    private $statistics;
    private $processingTime;
    private $errors;
    private $warnings;
    
    public function __construct()
    {
        $this->content = '';
        $this->metadata = [];
        $this->statistics = [];
        $this->processingTime = 0;
        $this->errors = [];
        $this->warnings = [];
    }
    
    /**
     * Set extracted content
     * 
     * @param string $content
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * Get extracted content
     * 
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
    
    /**
     * Set document metadata
     * 
     * @param array $metadata
     * @return self
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }
    
    /**
     * Get document metadata
     * 
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    
    /**
     * Add metadata item
     * 
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function addMetadata(string $key, $value): self
    {
        $this->metadata[$key] = $value;
        return $this;
    }
    
    /**
     * Set processing statistics
     * 
     * @param array $statistics
     * @return self
     */
    public function setStatistics(array $statistics): self
    {
        $this->statistics = $statistics;
        return $this;
    }
    
    /**
     * Get processing statistics
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        return $this->statistics;
    }
    
    /**
     * Add statistic
     * 
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function addStatistic(string $key, $value): self
    {
        $this->statistics[$key] = $value;
        return $this;
    }
    
    /**
     * Set processing time
     * 
     * @param float $time
     * @return self
     */
    public function setProcessingTime(float $time): self
    {
        $this->processingTime = $time;
        return $this;
    }
    
    /**
     * Get processing time
     * 
     * @return float
     */
    public function getProcessingTime(): float
    {
        return $this->processingTime;
    }
    
    /**
     * Add error
     * 
     * @param string $error
     * @return self
     */
    public function addError(string $error): self
    {
        $this->errors[] = $error;
        return $this;
    }
    
    /**
     * Get errors
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Add warning
     * 
     * @param string $warning
     * @return self
     */
    public function addWarning(string $warning): self
    {
        $this->warnings[] = $warning;
        return $this;
    }
    
    /**
     * Get warnings
     * 
     * @return array
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }
    
    /**
     * Check if processing was successful
     * 
     * @return bool
     */
    public function isSuccess(): bool
    {
        return empty($this->errors);
    }
    
    /**
     * Get word count
     * 
     * @return int
     */
    public function getWordCount(): int
    {
        return str_word_count($this->content);
    }
    
    /**
     * Get character count
     * 
     * @return int
     */
    public function getCharacterCount(): int
    {
        return strlen($this->content);
    }
    
    /**
     * Get line count
     * 
     * @return int
     */
    public function getLineCount(): int
    {
        return substr_count($this->content, "\n") + 1;
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'metadata' => $this->metadata,
            'statistics' => $this->statistics,
            'processing_time' => $this->processingTime,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'word_count' => $this->getWordCount(),
            'character_count' => $this->getCharacterCount(),
            'line_count' => $this->getLineCount(),
            'success' => $this->isSuccess()
        ];
    }
    
    /**
     * Convert to JSON
     * 
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
} 