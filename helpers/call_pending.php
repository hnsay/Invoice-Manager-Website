<?php
/**
 * Fetch ajax data for pending invoices.
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
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
require_once SESSION_HELPER;

protectPage(['superuser'], ['admin']);


$sql = "SELECT * FROM invoices WHERE state='Bekliyor'";
$result = mysqli_query($link, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['assignee'] == null) {
        $row['assignee'] = "Atanmamış";
    }

    $row['description'] = strip_tags($row['description']);

    $data[] = $row;
}

$data = '{"data":'.json_encode($data)."}";

echo $data;
?>