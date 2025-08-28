<?php
$file = 'app/Http/Controllers/Api/v1/BothServiceFormController.php';
$lines = file($file);

// Extract lines 820-850
for ($i = 819; $i < 850 && $i < count($lines); $i++) {
    echo ($i + 1) . ": " . $lines[$i];
}
?>