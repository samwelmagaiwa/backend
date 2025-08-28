<?php
// Simple syntax checker
$file = 'app/Http/Controllers/Api/v1/BothServiceFormController.php';
$content = file_get_contents($file);

// Try to parse the file
$result = php_check_syntax($file, $error_message);

if (!$result) {
    echo "Syntax error found:\n";
    echo $error_message . "\n";
} else {
    echo "No syntax errors found.\n";
}

// Alternative method using token_get_all
try {
    $tokens = token_get_all($content);
    echo "File parsed successfully with token_get_all\n";
} catch (ParseError $e) {
    echo "Parse error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}