<?php
/**
 * Session and user authorization checks.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <say@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
checkLogin();
initializeSession();

function checkLogin() {
    if (empty($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        $redirect = LOGIN_URL . '?location=' . urlencode($_SERVER['REQUEST_URI']);
        header("Location: $redirect");
        exit;
    }
}

function initializeSession() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function error403() {
    http_response_code(403);
    include $_SERVER['DOCUMENT_ROOT'] . '/app/views/403.php';
    exit;
}


function checkRole(array $roles) {
    return isset($_SESSION["usertype"]) && in_array($_SESSION["usertype"], $roles);
}

function protectPage(array $roles) {
    if (!checkRole($roles)) {
        http_response_code(403);
        include $_SERVER['DOCUMENT_ROOT'] . '/app/views/403.php';
        exit;
    }
}
?>