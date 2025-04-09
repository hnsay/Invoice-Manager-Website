<?php
/**
 * Fetch Ajax data for all invoices.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <say@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
// Initialize the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location:app/views/login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

if ($_SESSION["usertype"] != "superuser" && $_SESSION["usertype"] != "admin" ) {
      header("location: app/views/404.php");
      exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php"; 


$sql = "SELECT * FROM invoices";
$result = mysqli_query($link, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['assignee'] == null) {
        $row['assignee'] = "Atanmamış";
    }
    
    //$row['no'] = '<a href="invoice.php?' . $row['no'] . '=">' . $row['no'] . '</a>';

    $row['description'] = strip_tags($row['description']);

    $data[] = $row;
}

$data = '{"data":'.json_encode($data)."}";

echo $data;
?>