﻿<?php
/**
 * Process data submitted by process_bulk.php.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <github@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */

require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once SESSION_HELPER;
require_once MODEL_INVOICE;

protectPage(['superuser']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Toplu Fatura İşleme</title>
    <link rel="icon" type="image/x-icon" href="/public/icons/favicon.ico">

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
            processInvoiceAdmin($link, $no, "Atananın Yorumu: ", "\nFinans Yorumu: ".$_POST["commentFinance"]);
        }
            echo "Faturalar İşlendi";
    } else if (isset($_POST['reject'])) {
        foreach ($_POST['array'] as $no) {
            returnInvoiceAdmin($link, $no, "Atananın Yorumu: ", "\nFinans Yorumu: ".$_POST["commentFinance"]);
        }
        echo "Faturalar Geri Gönderildi";
    } else {
        echo "Yapılacak işlem belirlenemedi.";
    }
    //header("location: login.php");
    exit;
} else {
      header("location: 403.php");
      exit;
}
?>
</textarea>
</div>
</body>
</html>