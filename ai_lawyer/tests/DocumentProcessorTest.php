<?php
/**
 * DocumentProcessor Test
 * 
 * Simple test to verify the document processing system works correctly.
 * 
 * @author AI Lawyer System
 * @version 2.0
 */

require_once __DIR__ . '/../vendor/autoload.php';

use AILawyer\Classes\DocumentProcessor;
use AILawyer\Classes\DocumentResult;

class DocumentProcessorTest
{
    private $processor;
    
    public function __construct()
    {
        $this->processor = new DocumentProcessor();
    }
    
    /**
     * Test system initialization
     */
    public function testSystemInitialization()
    {
        echo "Testing system initialization...\n";
        
        $supportedFormats = $this->processor->getSupportedFormats();
        $expectedFormats = ['docx', 'doc', 'txt', 'rtf'];
        
        if (count(array_intersect($supportedFormats, $expectedFormats)) === count($expectedFormats)) {
            echo "✓ Supported formats: " . implode(', ', $supportedFormats) . "\n";
        } else {
            echo "✗ Supported formats mismatch\n";
            return false;
        }
        
        $maxFileSize = $this->processor->getMaxFileSize();
        if ($maxFileSize > 0) {
            echo "✓ Maximum file size: " . round($maxFileSize / 1024 / 1024, 1) . " MB\n";
        } else {
            echo "✗ Invalid maximum file size\n";
            return false;
        }
        
        return true;
    }
    
    /**
     * Test format detection
     */
    public function testFormatDetection()
    {
        echo "\nTesting format detection...\n";
        
        $testCases = [
            'document.docx' => true,
            'document.doc' => true,
            'document.txt' => true,
            'document.rtf' => true,
            'document.jpg' => false,
            'document.exe' => false
        ];
        
        foreach ($testCases as $filename => $expected) {
            $result = $this->processor->isFormatSupported($filename);
            if ($result === $expected) {
                echo "✓ {$filename}: " . ($expected ? 'supported' : 'not supported') . "\n";
            } else {
                echo "✗ {$filename}: expected " . ($expected ? 'supported' : 'not supported') . ", got " . ($result ? 'supported' : 'not supported') . "\n";
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Test error handling
     */
    public function testErrorHandling()
    {
        echo "\nTesting error handling...\n";
        
        // Test non-existent file
        try {
            $this->processor->processDocument('/non/existent/file.docx');
            echo "✗ Should have thrown exception for non-existent file\n";
            return false;
        } catch (Exception $e) {
            echo "✓ Correctly handled non-existent file: " . $e->getMessage() . "\n";
        }
        
        // Test unsupported format
        try {
            $this->processor->processDocument('/path/to/file.jpg');
            echo "✗ Should have thrown exception for unsupported format\n";
            return false;
        } catch (Exception $e) {
            echo "✓ Correctly handled unsupported format: " . $e->getMessage() . "\n";
        }
        
        return true;
    }
    
    /**
     * Test DocumentResult functionality
     */
    public function testDocumentResult()
    {
        echo "\nTesting DocumentResult functionality...\n";
        
        $result = new DocumentResult();
        
        // Test content setting
        $testContent = "This is a test document with some content.";
        $result->setContent($testContent);
        
        if ($result->getContent() === $testContent) {
            echo "✓ Content setting and retrieval works\n";
        } else {
            echo "✗ Content setting and retrieval failed\n";
            return false;
        }
        
        // Test metadata
        $testMetadata = ['title' => 'Test Document', 'author' => 'Test Author'];
        $result->setMetadata($testMetadata);
        
        if ($result->getMetadata() === $testMetadata) {
            echo "✓ Metadata setting and retrieval works\n";
        } else {
            echo "✗ Metadata setting and retrieval failed\n";
            return false;
        }
        
        // Test statistics
        $testStatistics = ['word_count' => 10, 'character_count' => 50];
        $result->setStatistics($testStatistics);
        
        if ($result->getStatistics() === $testStatistics) {
            echo "✓ Statistics setting and retrieval works\n";
        } else {
            echo "✗ Statistics setting and retrieval failed\n";
            return false;
        }
        
        // Test word count calculation
        $expectedWordCount = 9; // "This is a test document with some content."
        if ($result->getWordCount() === $expectedWordCount) {
            echo "✓ Word count calculation works\n";
        } else {
            echo "✗ Word count calculation failed (expected {$expectedWordCount}, got " . $result->getWordCount() . ")\n";
            return false;
        }
        
        // Test success status
        if ($result->isSuccess()) {
            echo "✓ Success status works correctly\n";
        } else {
            echo "✗ Success status failed\n";
            return false;
        }
        
        // Test error handling
        $result->addError("Test error");
        if (!$result->isSuccess()) {
            echo "✓ Error handling works correctly\n";
        } else {
            echo "✗ Error handling failed\n";
            return false;
        }
        
        return true;
    }
    
    /**
     * Run all tests
     */
    public function runAllTests()
    {
        echo "=== Document Processing System Tests ===\n\n";
        
        $tests = [
            'testSystemInitialization',
            'testFormatDetection', 
            'testErrorHandling',
            'testDocumentResult'
        ];
        
        $passed = 0;
        $total = count($tests);
        
        foreach ($tests as $test) {
            if ($this->$test()) {
                $passed++;
            }
        }
        
        echo "\n=== Test Results ===\n";
        echo "Passed: {$passed}/{$total}\n";
        
        if ($passed === $total) {
            echo "✓ All tests passed! System is working correctly.\n";
            return true;
        } else {
            echo "✗ Some tests failed. Please check the implementation.\n";
            return false;
        }
    }
}

// Run tests if this file is executed directly
if (php_sapi_name() === 'cli') {
    $test = new DocumentProcessorTest();
    $test->runAllTests();
} 