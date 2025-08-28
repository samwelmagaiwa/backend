<?php
$file = 'app/Http/Controllers/Api/v1/BothServiceFormController.php';
$content = file_get_contents($file);

// Check for syntax errors by trying to parse
try {
    $ast = token_get_all($content);
    echo "File parsed successfully\n";
} catch (ParseError $e) {
    echo "Parse error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    
    // Show the problematic line and surrounding context
    $lines = explode("\n", $content);
    $errorLine = $e->getLine() - 1; // 0-based index
    
    for ($i = max(0, $errorLine - 5); $i <= min(count($lines) - 1, $errorLine + 5); $i++) {
        $marker = ($i == $errorLine) ? " >>> " : "     ";
        echo sprintf("%s%4d: %s\n", $marker, $i + 1, $lines[$i]);
    }
}
?>