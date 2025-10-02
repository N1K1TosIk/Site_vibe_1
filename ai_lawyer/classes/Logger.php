<?php
/**
 * Logger - Simple logging utility
 * 
 * @author AI Lawyer System
 * @version 2.0
 */

namespace AILawyer\Classes;

class Logger
{
    private $logFile;
    
    public function __construct(string $logFile = null)
    {
        $this->logFile = $logFile ?? dirname(__DIR__) . '/logs/document_processing.log';
        $this->ensureLogDirectory();
    }
    
    /**
     * Ensure log directory exists
     */
    private function ensureLogDirectory(): void
    {
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Log info message
     * 
     * @param string $message
     * @param array $context
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }
    
    /**
     * Log error message
     * 
     * @param string $message
     * @param array $context
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }
    
    /**
     * Log warning message
     * 
     * @param string $message
     * @param array $context
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }
    
    /**
     * Log debug message
     * 
     * @param string $message
     * @param array $context
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }
    
    /**
     * Write log entry
     * 
     * @param string $level
     * @param string $message
     * @param array $context
     */
    private function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logEntry = "[{$timestamp}] {$level}: {$message}{$contextStr}" . PHP_EOL;
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
} 