<?php
/**
 * Setup script to create directories and check configuration
 * File: /individualnye-resheniya/setup.php
 */

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>EDS Individual Solutions - Setup Script</h1>";

$projectDir = $_SERVER['DOCUMENT_ROOT'] . '/individualnye-resheniya/';
$uploadsDir = $projectDir . 'uploads/';
$pdfDir = $projectDir . 'pdf/';
$logFile = $projectDir . 'debug.log';

echo "<h2>1. Creating Directories</h2>";

// Create uploads directory
if (!is_dir($uploadsDir)) {
    if (mkdir($uploadsDir, 0755, true)) {
        echo "✅ Created uploads directory: $uploadsDir<br>";
    } else {
        echo "❌ Failed to create uploads directory: $uploadsDir<br>";
    }
} else {
    echo "✅ Uploads directory already exists: $uploadsDir<br>";
}

// Create PDF directory
if (!is_dir($pdfDir)) {
    if (mkdir($pdfDir, 0755, true)) {
        echo "✅ Created PDF directory: $pdfDir<br>";
    } else {
        echo "❌ Failed to create PDF directory: $pdfDir<br>";
    }
} else {
    echo "✅ PDF directory already exists: $pdfDir<br>";
}

echo "<h2>2. Setting Permissions</h2>";

// Set proper permissions
if (is_dir($uploadsDir)) {
    chmod($uploadsDir, 0755);
    echo "✅ Set permissions 755 for uploads directory<br>";
}

if (is_dir($pdfDir)) {
    chmod($pdfDir, 0755);
    echo "✅ Set permissions 755 for PDF directory<br>";
}

// Create/check log file
if (!file_exists($logFile)) {
    if (touch($logFile)) {
        chmod($logFile, 0666);
        echo "✅ Created log file: $logFile<br>";
    } else {
        echo "❌ Failed to create log file: $logFile<br>";
    }
} else {
    echo "✅ Log file already exists: $logFile<br>";
}

echo "<h2>3. Checking Permissions</h2>";

// Check if directories are writable
echo "Uploads directory writable: " . (is_writable($uploadsDir) ? "✅ Yes" : "❌ No") . "<br>";
echo "PDF directory writable: " . (is_writable($pdfDir) ? "✅ Yes" : "❌ No") . "<br>";
echo "Log file writable: " . (is_writable($logFile) ? "✅ Yes" : "❌ No") . "<br>";

echo "<h2>4. Testing File Operations</h2>";

// Test file creation in uploads
$testUploadFile = $uploadsDir . 'test_' . date('His') . '.txt';
if (file_put_contents($testUploadFile, 'Test upload file')) {
    echo "✅ Successfully created test file in uploads: " . basename($testUploadFile) . "<br>";
    unlink($testUploadFile); // Clean up
} else {
    echo "❌ Failed to create test file in uploads<br>";
}

// Test file creation in PDF directory
$testPdfFile = $pdfDir . 'test_' . date('His') . '.txt';
if (file_put_contents($testPdfFile, 'Test PDF file')) {
    echo "✅ Successfully created test file in PDF: " . basename($testPdfFile) . "<br>";
    unlink($testPdfFile); // Clean up
} else {
    echo "❌ Failed to create test file in PDF directory<br>";
}

echo "<h2>5. Checking Bitrix Integration</h2>";

// Try to include Bitrix
try {
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    
    if (defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true) {
        echo "✅ Bitrix core loaded successfully<br>";
        
        // Check modules
        if (CModule::IncludeModule("iblock")) {
            echo "✅ IBlock module loaded<br>";
        } else {
            echo "❌ IBlock module not available<br>";
        }
        
        if (CModule::IncludeModule("main")) {
            echo "✅ Main module loaded<br>";
        } else {
            echo "❌ Main module not available<br>";
        }
        
    } else {
        echo "❌ Bitrix core not loaded properly<br>";
    }
} catch (Exception $e) {
    echo "❌ Error loading Bitrix: " . $e->getMessage() . "<br>";
}

echo "<h2>6. Email Configuration Check</h2>";

// Check email settings
if (function_exists('mail')) {
    echo "✅ PHP mail() function available<br>";
} else {
    echo "❌ PHP mail() function not available<br>";
}

// Check if we can determine sender email
$senderEmail = 'noreply@edsy.ru'; // Default sender email
echo "📧 Default sender email: $senderEmail<br>";
echo "📧 Manager notification email: orders@edsy.ru<br>";

echo "<h2>7. Configuration Summary</h2>";

echo "<pre>";
echo "Project Directory: $projectDir\n";
echo "Uploads Directory: $uploadsDir\n";
echo "PDF Directory: $pdfDir\n";
echo "Log File: $logFile\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Max Upload Size: " . ini_get('upload_max_filesize') . "\n";
echo "Max Post Size: " . ini_get('post_max_size') . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "</pre>";

echo "<h2>8. Next Steps</h2>";
echo "<ol>";
echo "<li>✅ Replace handler.php with the new version</li>";
echo "<li>📁 Files will be saved to: <code>$uploadsDir</code></li>";
echo "<li>📄 PDFs will be saved to: <code>$pdfDir</code></li>";
echo "<li>📧 Emails will be sent to client and orders@edsy.ru</li>";
echo "<li>🗂️ IBlock 'EDS Leads' will be created automatically</li>";
echo "</ol>";

echo "<p><strong>✅ Setup completed! You can now test the form.</strong></p>";
?>
