<?php
echo "Fixing BothServiceFormController syntax error...\n";

$file = 'app/Http/Controllers/Api/v1/BothServiceFormController.php';
$content = file_get_contents($file);

// Multiple possible problematic patterns to fix
$patterns = [
    // Pattern 1: Orphaned can_edit and is_readonly
    [
        'search' => "                ],\n\n                    'can_edit' => false,\n                    'is_readonly' => true\n                ],",
        'replace' => "                ],\n                'hod_it_section' => [\n                    'approval_status' => null,\n                    'comments' => null,\n                    'approved_at' => null,\n                    'approved_by' => null,\n                    'can_edit' => false,\n                    'is_readonly' => true\n                ],"
    ],
    // Pattern 2: Missing section between dict and ict_officer
    [
        'search' => "                'dict_section' => [\n                    'approval_status' => \$form->dict_approval_status,\n                    'comments' => \$form->dict_comments,\n                    'approved_at' => \$form->dict_approved_at,\n                    'approved_by' => \$form->dictUser?->name,\n                    'can_edit' => false,\n                    'is_readonly' => true\n                ],\n                'ict_officer_section' => [",
        'replace' => "                'dict_section' => [\n                    'approval_status' => \$form->dict_approval_status,\n                    'comments' => \$form->dict_comments,\n                    'approved_at' => \$form->dict_approved_at,\n                    'approved_by' => \$form->dictUser?->name,\n                    'can_edit' => false,\n                    'is_readonly' => true\n                ],\n                'hod_it_section' => [\n                    'approval_status' => null,\n                    'comments' => null,\n                    'approved_at' => null,\n                    'approved_by' => null,\n                    'can_edit' => false,\n                    'is_readonly' => true\n                ],\n                'ict_officer_section' => ["
    ]
];

$fixed = false;
foreach ($patterns as $pattern) {
    $new_content = str_replace($pattern['search'], $pattern['replace'], $content);
    if ($new_content !== $content) {
        $content = $new_content;
        $fixed = true;
        echo "Applied fix pattern\n";
        break;
    }
}

if ($fixed) {
    file_put_contents($file, $content);
    echo "✅ BothServiceFormController syntax fixed!\n";
} else {
    echo "❌ Could not automatically fix. Manual intervention needed.\n";
    
    // Show lines around potential problem areas
    $lines = explode("\n", $content);
    echo "\nShowing lines 830-840:\n";
    for ($i = 829; $i < 840; $i++) {
        if (isset($lines[$i])) {
            echo sprintf("%4d: %s\n", $i + 1, $lines[$i]);
        }
    }
}

// Test if file can be parsed now
try {
    $tokens = token_get_all($content);
    echo "✅ File syntax is now valid!\n";
} catch (ParseError $e) {
    echo "❌ Syntax error still exists: " . $e->getMessage() . " on line " . $e->getLine() . "\n";
}
?>