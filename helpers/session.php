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
initializeSession();
checkLogin();

function checkLogin() {
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        $redirectUrl = URL_LOGIN;
        $requestedUrl = $_SERVER['REQUEST_URI'];
        if (strpos($requestedUrl, 'http://') !== false || strpos($requestedUrl, 'https://') !== false) {
            $requestedUrl = '/';
        }

        $allowedPaths = ['/dashboard', '/profile', '/settings'];
        if (!in_array(parse_url($requestedUrl, PHP_URL_PATH), $allowedPaths)) {
            $requestedUrl = '/';
        }

        $redirectUrl .= '?location=' . urlencode($requestedUrl);
        
        header("Location: $redirectUrl");
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