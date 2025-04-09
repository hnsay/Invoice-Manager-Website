<?php
phpinfo();
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
// Deliberate error: calling an undefined function
//undefined_function_call();

echo "This line will not be reached.";
?>