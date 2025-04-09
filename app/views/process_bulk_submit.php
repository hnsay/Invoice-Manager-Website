<?php
/**
 * Process data submitted by process_bulk.php.
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

if ($_SESSION["usertype"] != "superuser" && $_SESSION["usertype"] != "admin" && $invoice['assignee'] != $_SESSION["username"] && $invoice['assignee'] != $mailgroup) {
      header("location: /app/views/404.php");
      exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Toplu Fatura İşleme</title>
    <link rel="stylesheet" href="/public/Datatables/datatables.css"/>
    <style type="text/css">

body{ font: 12px sans-serif; text-align: center; }
div.dtsp-title, div.dtsp-topRow {
  display: none;
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

span {
  font-style: italic
}

th {
  background: #9F2725;
  color: #ffffff;
}

th {
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
}

.wrapper{ width: 600px; padding: 20px; text-align: left; }

</style>
</head>

<?php require 'navbar.php'; ?>

<body>
<h1>Toplu Fatura Onaylama</h1> <br>
<div class="wrapper" style="padding-left: 50px;">
            <textarea readonly style="margin-bottom:20px;" type="text" name="comment" class="form-control" rows="20" id="first">
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['process'])) {
        foreach ($_POST['array'] as $no) {
            Process_invoice($link, $no, "Atananın Yorumu: ", "\nFinans Yorumu: ".$_POST["commentFinance"]);
        }
            echo "Faturalar İşlendi";
    } else if (isset($_POST['reject'])) {
        foreach ($_POST['array'] as $no) {
            Return_invoice($link, $no, "Atananın Yorumu: ", "\nFinans Yorumu: ".$_POST["commentFinance"]);
        }
        echo "Faturalar Geri Gönderildi";
    } else {
        echo "Yapılacak işlem belirlenemedi.";
    }
    //header("location: login.php");
} else {
      header("location: 404.php");
}


function Process_invoice($link, $no, $comment, $comment2)
{
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state='İşlenmiş', comment=CONCAT(?, comment, ?) WHERE no=?");
    mysqli_stmt_bind_param($stmt, "sss", $comment, $comment2, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function Return_invoice($link, $no, $comment, $comment2)
{
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state='Bekliyor', comment=CONCAT(?, comment, ?) WHERE no=?");
    mysqli_stmt_bind_param($stmt, "sss", $comment, $comment2, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>
</textarea>
</div>
</body>
</html>