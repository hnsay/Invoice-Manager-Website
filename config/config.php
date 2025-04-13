<?php
/**
 * Establish SQL connection.
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

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'test');
define('DB_NAME', 'invoice_tracker_test');

define('APP_PATH', $_SERVER['DOCUMENT_ROOT'] . '/app');

define('APP_CONTROLLERS', APP_PATH . '/controllers');
define('APP_MODELS', APP_PATH . '/models');
define('APP_VIEWS', APP_PATH . '/views');

define('VIEW_403', APP_VIEWS . '/403.php');
define('VIEW_ALLINVOICES', APP_VIEWS . '/allinvoices.php');
define('VIEW_APPROVE_BULK', APP_VIEWS . '/approve_bulk.php');
define('VIEW_APPROVE_BULK_SUBMIT', APP_VIEWS . '/approve_bulk_submit.php');
define('VIEW_CREATEMAILGROUP', APP_VIEWS . '/createmailgroup.php');
define('VIEW_DELETEUSER', APP_VIEWS . '/deleteuser.php');
define('VIEW_EDITUSERS', APP_VIEWS . '/editusers.php');
define('VIEW_INVOICE', APP_VIEWS . '/invoice.php');
define('VIEW_LOGIN', APP_VIEWS . '/login.php');
define('VIEW_MANAGEUSERS', APP_VIEWS . '/manageusers.php');
define('VIEW_MANAGE_ALLINVOICES', APP_VIEWS . '/manage_allinvoices.php');
define('VIEW_NAVBAR', APP_VIEWS . '/navbar.php');
define('VIEW_PENDING', APP_VIEWS . '/pending.php');
define('VIEW_PENDING_FINANCE', APP_VIEWS . '/pending_finance.php');
define('VIEW_PROCESS_BULK', APP_VIEWS . '/process_bulk.php');
define('VIEW_PROCESS_BULK_SUBMIT', APP_VIEWS . '/process_bulk_submit.php');
define('VIEW_PROFILE', APP_VIEWS . '/profile.php');
define('VIEW_REGISTER', APP_VIEWS . '/register.php');
define('VIEW_RESET_PASSWORD', APP_VIEWS . '/reset-password.php');
define('VIEW_RESET_USER', APP_VIEWS . '/reset-user.php');
define('VIEW_RULES', APP_VIEWS . '/rules.php');
define('VIEW_SUBMIT', APP_VIEWS . '/submit.php');
define('VIEW_UPLOAD', APP_VIEWS . '/upload.php');
define('VIEW_WELCOME', APP_VIEWS . '/welcome.php');

define('HELPERS_PATH', $_SERVER['DOCUMENT_ROOT'] . '/helpers');

define('ASSIGN_HELPER', HELPERS_PATH . '/assign.php');
define('CALL_HELPER', HELPERS_PATH . '/call.php');
define('CALL_PENDING_HELPER', HELPERS_PATH . '/call_pending.php');
define('CALL_PENDING_FINANCE_HELPER', HELPERS_PATH . '/call_pending_finance.php');
define('LOGOUT_HELPER', HELPERS_PATH . '/logout.php');
define('SESSION_HELPER', HELPERS_PATH . '/session.php');
define('TABLE_MANAGE_HELPER', HELPERS_PATH . '/table_manage.php');
define('TABLE_PROCESS_HELPER', HELPERS_PATH . '/table_process.php');
define('TABLE_PROCESS_LITE_HELPER', HELPERS_PATH . '/table_process_lite.php');

define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/public');

 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
