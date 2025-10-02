<?php
/**
 * DocumentExtractorInterface - Interface for document extractors
 * 
 * This interface defines the contract that all document extractors must implement.
 * 
 * @author AI Lawyer System
 * @version 2.0
 */

namespace AILawyer\Classes;

interface DocumentExtractorInterface
{
    /**
     * Extract content from a document file
     * 
     * @param string $filePath Path to the document file
     * @param array $options Processing options
     * @return DocumentResult
     * @throws Exception
     */
    public function extract(string $filePath, array $options = []): DocumentResult;
    
    /**
     * Get supported formats for this extractor
     * 
     * @return array
     */
    public function getSupportedFormats(): array;
    
    /**
     * Check if this extractor can handle the given file
     * 
     * @param string $filePath
     * @return bool
     */
    public function canHandle(string $filePath): bool;
    
    /**
     * Get extractor metadata
     * 
     * @return array
     */
    public function getMetadata(): array;
} 