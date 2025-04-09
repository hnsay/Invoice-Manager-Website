<?php
/**
 * Process data submitted by approve_bulk.php.
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
      header("location: 404.php");
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
    if (isset($_POST['approve'])) {
        if (isset($_POST['checkBox']) && $_POST['checkBox'] == "on") {
            foreach ($_POST['array'] as $no) {
                Approve_Reject_invoice($link, trim($no), "Concur", "Concur üzerinden işlenecek", $_POST["ponumber"]);
            }
        } else {
            foreach ($_POST['array'] as $no) {
                Approve_Reject_invoice($link, trim($no), "Onaylanmış", $_POST["comment"], $_POST["ponumber"]);
            }
        }

    } else if (isset($_POST['reject'])) {
        foreach ($_POST['array'] as $no) {
                Approve_Reject_invoice($link, trim($no), "Reddedilmiş", $_POST["comment"], $_POST["ponumber"]);
        }
    } else {
        echo "Yapılacak işlem belirlenemedi.";
    }
    //header("location: login.php");
} else {
      header("location: 404.php");
}


function Check_details($link, $no)
{    
      $sql = "SELECT assignee, state FROM invoices where no = " . "'" . $no . "'";
    $result = mysqli_query($link, $sql);
    $invoice =  mysqli_fetch_array($result);
    
      $sql = "SELECT mailgroup FROM users WHERE username=". "'" . $_SESSION["username"] . "'";
    $result = mysqli_query($link, $sql);
    $mailgroup = mysqli_fetch_array($result)['mailgroup'];
    
    if ($invoice['state'] == "Bekliyor") {
        if ($_SESSION["usertype"] == "superuser" || $_SESSION["usertype"] == "admin" || $invoice['assignee'] == $_SESSION["username"] || $invoice['assignee'] == $mailgroup) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return -1;
    }
    
}

function Approve_Reject_invoice($link, $no, $state, $comment, $po)
{
    $detail_check = Check_details($link, $no);
    if ($detail_check == 1) {
        $stmt = mysqli_prepare($link, "UPDATE invoices SET state=?, comment=?, po_rfa=? WHERE no=?");
        mysqli_stmt_bind_param($stmt, "ssss", $state, $comment, $po, $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo $no." için işlem başarıyla tamamlandı, durum: ".$state." PO: ".$po."\n";
    } else if ($detail_check == 0) {
        echo $no." için işlem başarısız, fatura size veya grubunuza atanmamış.\n";
    } else if ($detail_check == -1) {
        echo $no." için işlem başarısız, bu faturada daha önce işlem yapılmış.\n";
    } else {
        echo $no." için işlem başarısız, error 302 , bir hata oluştu.\n";
    }
}
?>
</textarea>
</div>
</body>
</html>