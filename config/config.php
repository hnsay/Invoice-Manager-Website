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
define('LOGIN_URL', '/app/views/login.php');
define('LOGOUT_URL', '/hepers/logout.php');
define('CALL_URL', '/helpers/call.php');
define('CALL_PENDING_URL', '/helpers/call_pending.php');
define('CALL_PENDING_FINANCE_URL', '/helpers/call_pending_finance.php');
define('TABLE_MANAGE_URL', '/helpers/table_manage.php.php');
define('TABLE_PROCESS_URL', '/helpers/table_process.php');
define('TABLE_PROCESS_LITE_URL', '/helpers/table_process_lite.php');
 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
