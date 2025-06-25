<?php
echo "Testing CodeIgniter...<br>";
try {
    require_once 'vendor/autoload.php';
    echo "Autoload OK<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>