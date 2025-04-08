<?php
/**
 * Display and manage all invoices.
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
      header("Location:login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
      exit;
}

if ($_SESSION["usertype"] != "superuser" && $_SESSION["usertype"] != "admin" ) {
      header("location: 404.php");
      exit;
}

require_once "config/config.php";
require_once "error_log.php"; 


$sql = "SELECT * FROM invoices";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result);

$sql2 = "SELECT username FROM users";
$result2 = mysqli_query($link, $sql2);
mysqli_close($link);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tüm Faturalar</title>


    <script src="Datatables/datatables.min.js"></script>
    <script src="Datatables/moment.min.js"></script>
    <script src="Datatables/dataTables.checkboxes.min.js"></script>
    <script src="Datatables/jquery.dataTables.colResize.js"></script>
    <script src="Datatables/select2.min.js"></script>
    <?php //<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> ?>
    <link rel="stylesheet" href="Datatables/datatables.css"/>
    <link rel="stylesheet" href="css/jquery.dataTables.css">
    <?php //<link rel="stylesheet" href="css/dataTables.checkboxes.css"> ?>
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <link rel="stylesheet" href="styles.css">
    <?php //<link rel="stylesheet" href="css/jquery.dataTables.colResize.css"> ?>
    <link rel="stylesheet" type="text/css" href="css/select2.min.css">
 
  

<style type="text/css">

body{ font: 12px sans-serif; text-align: center; }
div.dtsp-title, div.dtsp-topRow {
  display: none;
}
:root {
--dt-row-selected: 250, 150, 3;
}
table {
  width: 100%;
  border-collapse: collapse;

}

.navbar {
      margin-bottom: 0;
      border-radius: 0;
}


th {
  background: #9F2725;
  border: 1px solid #ccc;
}

td {
  border: 1px solid #ccc;
}

table.dataTable tbody td {
  vertical-align: middle;
}

table.dataTable thead th {
  text-align: center;
  vertical-align: middle;
}
.dataTable:focus {
  outline: 0 !important;
}




.toolbar {
  float: left;
}


.dataTables_info {
  text-style: italic;
}

.inline {
  display: inline;
}

.link-button {
  background: none;
  border: none;
  color: #0070E0;
  cursor: pointer;
}
.link-button:focus {
  outline: none;
}
.link-button:active {
  color:red;
}

</style>

</head>

<?php require 'navbar.php'; ?>

<body>

<div style="padding-left: 30px;padding-right: 20px;margin-top: 50px;display: inline-block;">
<table style="width:100%" class="table responsive" id="sorTable" tabindex="0">
  <thead>
    <tr>
      <th data-priority="1" scope="col" style="width: 1%;"></th>
      <th data-priority="2" scope="col" style="width: 10%;">Tedarikçi</th>
      <th data-priority="3" scope="col" style="color: #ffffff;" style="width: 10%;">Fatura No</th>
      <th data-priority="4" scope="col" style="width: 1%;">Atanan</th>
      <th data-priority="5" scope="col" style="width: 1%;">Durum</th>
      <th data-priority="6" scope="col" style="width: 6%;">
      <input type="text" id="min" name="min" style="margin-bottom: 5px;" value="Başlangıç">
      <input type="text" id="max" name="max" value="Bitiş">
      </th>
      <th style="color: #ffffff;" scope="col" style="width: 1%;">Tutar</th>
        <th scope="col" style="width: 1%;">PB</th>
        <th style="color: #ffffff;" scope="col" style="width: 1%;">Yorum</th>
        <th style="color: #ffffff;" scope="col" style="width: 1%;">Order/RFA</th>
        <th style="color: #ffffff;" scope="col" style="width: 1%;">Açıklama</th>
        <th style="color: #ffffff;" scope="col" style="width: 1%;"></th>
  </tr>


  </thead>

  <tbody>
    <tr>
      <td data-table-header="#"></td>
      <td data-table-header="Tedarikçi"></td>
      <td data-table-header="Fatura No"></td>
      <td data-table-header="Atanan"></td>
      <td data-table-header="Durum"></td>
      <td data-table-header="Tarih"></td>
      <td data-table-header="Tutar"></td>
      <td data-table-header="PB"></td>
      <td data-table-header="Yorum"></td>
      <td data-table-header="PO"></td>
      <td data-table-header="Açıklama"></td>
      <td data-table-header=""></td>
    </tr>
  </tbody>
</table>
</div>
<?php require 'table_manage.php'; ?>
</body>
</html>
