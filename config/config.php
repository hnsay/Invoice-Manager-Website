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

define('URL_403', '/app/views/403.php');
define('URL_ALL_INVOICES', '/app/views/allinvoices.php');
define('URL_APPROVE_BULK', '/app/views/approve_bulk.php');
define('URL_APPROVE_BULK_SUBMIT', '/app/views/approve_bulk_submit.php');
define('URL_CREATE_MAIL_GROUP', '/app/views/createmailgroup.php');
define('URL_DELETE_USER', '/app/views/deleteuser.php');
define('URL_EDIT_USERS', '/app/views/editusers.php');
define('URL_INVOICE', '/app/views/invoice.php');
define('URL_LOGIN', '/app/views/login.php');
define('URL_MANAGE_USERS', '/app/views/manageusers.php');
define('URL_MANAGE_ALL_INVOICES', '/app/views/manage_allinvoices.php');
define('URL_NAVBAR', '/app/views/navbar.php');
define('URL_PENDING', '/app/views/pending.php');
define('URL_PENDING_FINANCE', '/app/views/pending_finance.php');
define('URL_PROCESS_BULK', '/app/views/process_bulk.php');
define('URL_PROCESS_BULK_SUBMIT', '/app/views/process_bulk_submit.php');
define('URL_PROFILE', '/app/views/profile.php');
define('URL_REGISTER', '/app/views/register.php');
define('URL_RESET_PASSWORD', '/app/views/reset-password.php');
define('URL_RESET_USER', '/app/views/reset-user.php');
define('URL_RULES', '/app/views/rules.php');
define('URL_SUBMIT', '/app/views/submit.php');
define('URL_UPLOAD', '/app/views/upload.php');
define('URL_WELCOME', '/app/views/welcome.php');

define('MODELS_PATH', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'models');

define('MODEL_INVOICE', MODELS_PATH . DIRECTORY_SEPARATOR . 'model_invoice.php');
define('MODEL_USER', MODELS_PATH . DIRECTORY_SEPARATOR . 'model_user.php');

define('HELPERS_PATH', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'helpers');

define('ASSIGN_HELPER', HELPERS_PATH . DIRECTORY_SEPARATOR . 'assign.php');
define('CALL_HELPER', HELPERS_PATH . DIRECTORY_SEPARATOR . 'call.php');
define('CALL_PENDING_HELPER', HELPERS_PATH . DIRECTORY_SEPARATOR . 'call_pending.php');
define('CALL_PENDING_FINANCE_HELPER', HELPERS_PATH . DIRECTORY_SEPARATOR . 'call_pending_finance.php');
define('LOGOUT_HELPER', HELPERS_PATH . DIRECTORY_SEPARATOR . 'logout.php');
define('SESSION_HELPER', HELPERS_PATH . DIRECTORY_SEPARATOR . 'session.php');
define('TABLE_APPROVE_HELPER', HELPERS_PATH . DIRECTORY_SEPARATOR . 'table_approve.php');
define('TABLE_MANAGE_HELPER', HELPERS_PATH . DIRECTORY_SEPARATOR . 'table_manage.php');
define('TABLE_PROCESS_HELPER', HELPERS_PATH . DIRECTORY_SEPARATOR . 'table_process.php');
define('TABLE_PROCESS_LITE_HELPER', HELPERS_PATH . DIRECTORY_SEPARATOR . 'table_process_lite.php');

define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'public');

 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
