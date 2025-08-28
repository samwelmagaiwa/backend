<?php
// Read the file
$file = 'app/Http/Controllers/Api/v1/BothServiceFormController.php';
$content = file_get_contents($file);

// Find and fix the problematic section
$problematic_pattern = "/(\],\s*\n\s*'can_edit' => false,\s*\n\s*'is_readonly' => true\s*\n\s*\],)/";

// Replace with proper structure
$replacement = "],\n                'hod_it_section' => [\n                    'approval_status' => null, // HOD_IT role removed\n                    'comments' => null,\n                    'approved_at' => null,\n                    'approved_by' => null,\n                    'can_edit' => false,\n                    'is_readonly' => true\n                ],";

$fixed_content = preg_replace($problematic_pattern, $replacement, $content);

if ($fixed_content !== $content) {
    file_put_contents($file, $fixed_content);
    echo "File fixed successfully!\n";
} else {
    echo "No changes needed or pattern not found.\n";
}
?>