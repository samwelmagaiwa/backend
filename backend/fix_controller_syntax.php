<?php
// Fix the syntax error in BothServiceFormController
$file = 'app/Http/Controllers/Api/v1/BothServiceFormController.php';
$content = file_get_contents($file);

// Find the problematic section and fix it
$search = "                ],\n\n                    'can_edit' => false,\n                    'is_readonly' => true\n                ],";

$replace = "                ],\n                'hod_it_section' => [\n                    'approval_status' => null, // HOD_IT role removed\n                    'comments' => null,\n                    'approved_at' => null,\n                    'approved_by' => null,\n                    'can_edit' => false,\n                    'is_readonly' => true\n                ],";

$fixed_content = str_replace($search, $replace, $content);

if ($fixed_content !== $content) {
    file_put_contents($file, $fixed_content);
    echo "BothServiceFormController syntax fixed!\n";
} else {
    echo "Pattern not found. Manual fix needed.\n";
    
    // Show the problematic area
    $lines = explode("\n", $content);
    for ($i = 830; $i < 840; $i++) {
        if (isset($lines[$i])) {
            echo ($i + 1) . ": " . $lines[$i] . "\n";
        }
    }
}
?>