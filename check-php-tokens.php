<?php
$path = "app/Http/Controllers/EventEquipmentController.php";
$source = file_get_contents($path);

foreach (token_get_all($source) as $token) {
    if (is_array($token)) {
        echo token_name($token[0]) . " | ";
        echo str_replace("\n", "\\n", $token[1]);
        echo PHP_EOL;
    } else {
        echo "CHAR | " . $token . PHP_EOL;
    }
}
