<?php
define('ABSPATH', 'C:/Users/Serkan/Local Sites/emdief-home/app/public/');
function is_admin() { return true; }
function current_user_can() { return true; }
try {
    require 'C:/Users/Serkan/.gemini/antigravity/scratch/mis360-furniture/inc/performance.php';
    mis360_performance_update_htaccess_rules();
} catch (Throwable \) {
    echo 'CAUGHT: ' . \->getMessage() . ' at ' . \->getFile() . ':' . \->getLine() . PHP_EOL;
}
