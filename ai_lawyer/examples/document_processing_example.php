<?php
/**
 * Document Processing Example
 * 
 * This example demonstrates how to use the new document processing system.
 * 
 * @author AI Lawyer System
 * @version 2.0
 */

require_once __DIR__ . '/../vendor/autoload.php';

use AILawyer\Classes\DocumentProcessor;
use AILawyer\Classes\DocumentResult;

// Initialize the document processor
$processor = new DocumentProcessor();

// Example 1: Process a DOCX document
echo "=== DOCX Processing Example ===\n";
try {
    $docxFile = __DIR__ . '/../uploads/documents/sample.docx';
    if (file_exists($docxFile)) {
        $result = $processor->processDocument($docxFile);
        
        echo "Success: " . ($result->isSuccess() ? 'Yes' : 'No') . "\n";
        echo "Content length: " . strlen($result->getContent()) . " characters\n";
        echo "Word count: " . $result->getWordCount() . "\n";
        echo "Processing time: " . round($result->getProcessingTime(), 3) . " seconds\n";
        
        $metadata = $result->getMetadata();
        if (!empty($metadata['title'])) {
            echo "Title: " . $metadata['title'] . "\n";
        }
        if (!empty($metadata['author'])) {
            echo "Author: " . $metadata['author'] . "\n";
        }
        
        echo "\nFirst 500 characters of content:\n";
        echo substr($result->getContent(), 0, 500) . "...\n\n";
    } else {
        echo "DOCX file not found: {$docxFile}\n";
    }
} catch (Exception $e) {
    echo "Error processing DOCX: " . $e->getMessage() . "\n";
}

// Example 2: Process a Word document
echo "=== Word Document Processing Example ===\n";
try {
    $wordFile = __DIR__ . '/../uploads/documents/sample.docx';
    if (file_exists($wordFile)) {
        $result = $processor->processDocument($wordFile);
        
        echo "Success: " . ($result->isSuccess() ? 'Yes' : 'No') . "\n";
        echo "Content length: " . strlen($result->getContent()) . " characters\n";
        echo "Word count: " . $result->getWordCount() . "\n";
        echo "Processing time: " . round($result->getProcessingTime(), 3) . " seconds\n";
        
        $statistics = $result->getStatistics();
        if (isset($statistics['section_count'])) {
            echo "Sections: " . $statistics['section_count'] . "\n";
        }
        
        echo "\nFirst 500 characters of content:\n";
        echo substr($result->getContent(), 0, 500) . "...\n\n";
    } else {
        echo "Word file not found: {$wordFile}\n";
    }
} catch (Exception $e) {
    echo "Error processing Word document: " . $e->getMessage() . "\n";
}

// Example 3: Process a text file
echo "=== Text File Processing Example ===\n";
try {
    $textFile = __DIR__ . '/../uploads/documents/sample.txt';
    if (file_exists($textFile)) {
        $result = $processor->processDocument($textFile);
        
        echo "Success: " . ($result->isSuccess() ? 'Yes' : 'No') . "\n";
        echo "Content length: " . strlen($result->getContent()) . " characters\n";
        echo "Word count: " . $result->getWordCount() . "\n";
        echo "Processing time: " . round($result->getProcessingTime(), 3) . " seconds\n";
        
        $metadata = $result->getMetadata();
        if (isset($metadata['encoding'])) {
            echo "Encoding: " . $metadata['encoding'] . "\n";
        }
        
        echo "\nFirst 500 characters of content:\n";
        echo substr($result->getContent(), 0, 500) . "...\n\n";
    } else {
        echo "Text file not found: {$textFile}\n";
    }
} catch (Exception $e) {
    echo "Error processing text file: " . $e->getMessage() . "\n";
}

// Example 4: Get system information
echo "=== System Information ===\n";
echo "Supported formats: " . implode(', ', $processor->getSupportedFormats()) . "\n";
echo "Maximum file size: " . round($processor->getMaxFileSize() / 1024 / 1024, 1) . " MB\n";

// Example 5: Batch processing
echo "\n=== Batch Processing Example ===\n";
$testFiles = [
    __DIR__ . '/../uploads/documents/sample.docx',
    __DIR__ . '/../uploads/documents/sample.txt'
];

$successCount = 0;
$totalProcessingTime = 0;

foreach ($testFiles as $file) {
    if (file_exists($file)) {
        try {
            $startTime = microtime(true);
            $result = $processor->processDocument($file);
            $processingTime = microtime(true) - $startTime;
            
            if ($result->isSuccess()) {
                $successCount++;
                $totalProcessingTime += $processingTime;
                
                echo "✓ " . basename($file) . " - " . $result->getWordCount() . " words\n";
            } else {
                echo "✗ " . basename($file) . " - Failed\n";
            }
        } catch (Exception $e) {
            echo "✗ " . basename($file) . " - Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "- " . basename($file) . " - Not found\n";
    }
}

echo "\nBatch processing summary:\n";
echo "Successfully processed: {$successCount}/" . count($testFiles) . " files\n";
echo "Total processing time: " . round($totalProcessingTime, 3) . " seconds\n";
echo "Average processing time: " . ($successCount > 0 ? round($totalProcessingTime / $successCount, 3) : 0) . " seconds per file\n";

echo "\n=== Example completed ===\n"; 