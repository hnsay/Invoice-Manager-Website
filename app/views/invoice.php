<?php
/**
 * Display and manage a single invoice.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <say@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
//start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
      header("Location:login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
      exit;
}

//get database config
require_once "config/config.php";
require_once "error_log.php";

//set invoice number to "null"
$no = 'null';
$comment = "";
$assignee = "";

//get the invoice number from post of welcome.php

if (empty($_GET)) {
    header("location: welcome.php");
    exit;
}

foreach ($_GET as $key=>$value) {
    $no = "$key";
}

$sql = "SELECT mailgroup FROM users WHERE username=". "'" . $_SESSION["username"] . "'";
$result = mysqli_query($link, $sql);
$mailgroup = mysqli_fetch_array($result)['mailgroup'];

$sql = "SELECT * FROM invoices where no = " . "'" . $no . "'";
$result = mysqli_query($link, $sql);
$invoice = mysqli_fetch_array($result);

if ($_SESSION["usertype"] != "superuser" && $_SESSION["usertype"] != "admin" && $invoice['assignee'] != $_SESSION["username"] && $invoice['assignee'] != $mailgroup) {
    header("location: 404.php");
    exit;
}

$sql = "SELECT * FROM invoicelines where no = " . "'" . $no . "'";
$result = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html lang="en" style="background-image: url('background.jpg');">
<head>
  <meta charset="UTF-8">
  <title><?php echo $no;?></title>
  <link rel="stylesheet" href="styles.css">
  <style type="text/css">
    body{ font: 9px sans-serif; text-align: center;}
    table{
      font-family: arial, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }
    td{
       border: 2px solid #dddddd;
       text-align: left;
       font-size:small;          
    }
    th{
         border: 2px solid #dddddd;
         text-align: left;
         font-size:small;
    }        
    .header {
      padding-top: 50px;
      padding-bottom: 25px;
    }
    .first-column {
      font-size: 2rem;
      display: flex;
      align-items: left;
      justify-content: flex-start;
    }
    .second-column {
      font-size: 2rem;
      display: flex;
      align-items: right;
      justify-content: right;
    }
    .row {
        border-width:2px;
        border-color:#000;  
        border: 1px solid black;
    }
    body{ font: 14px sans-serif; text-align: left; }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .navbar {
      margin-bottom: 0;
      border-radius: 0;
    }
    /* Zebra striping */
    tr:nth-of-type(odd) {
      background: #f4f4f4;
    }
    tr:nth-of-type(even) {
      background: #fff;
    }
    th {
      background: #782f40;
      color: #ffffff;
      font-weight: 300;
    }
    td,
    th {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: left;
    }
    td:nth-of-type(1) {
      font-weight: 500 !important;
    }
    td {
      font-family: 'Roboto', sans-serif !important;
      font-weight: 300;
      line-height: 20px;
    }
    span {
      font-style: italic
    }
  </style>
</head>

<body>

<div class="container" style="padding-top:30px; padding-bottom:30px;">
  <div class="row header">
    <div class="col-xs-12">
      <h1><?php echo $no;?></h1>
    </div>
  </div>
  
  <div class="row">
    <div class="col-sm-6 first-column">
      <div>
        <h5><b>Fatura No: </b><?php echo $no; ?></h5>
        <h5><b>Tedarikçi: </b><?php echo $invoice['supplier']; ?></h5>
        <h5><b>Durum: </b><?php echo $invoice['state']; ?><h5>
        <h5><b>Tutar: </b><?php echo $invoice['amount']." ".$invoice['currency']; ?></h5>
        <h5><b>Atanan: </b><?php echo $invoice['assignee'];?></h5>
        <h5><b>Yorumlar: </b><?php echo $invoice['comment'];?></h5>
        <h5><b>PO/RFA: </b><?php echo $invoice['po_rfa'];?></h5>
      </div>
    </div>
    <div class="col-sm-6 second-column" style="text-align: right;">
    <form method="post" class="inline" action="/submit.php">
    <h5><b>Fatura Tarihi: </b><?php echo $invoice['date']; ?></h5><br>
                    <input type="hidden"/>
                        <button type="submit" name="<?php echo $no;?>" class="btn btn-success">
                                İşlem Yap
                        </button>
              <a class="btn btn-primary" href="<?php echo $_SERVER['HTTP_REFERER']; ?>">Geri</a>
        </form>
    </div>
  </div>
  <div class="row">
  

<div style="margin-top: 50px;">
  <table class="table table-condensed">
                  <tr>
                        <th><p>Ürün/Hizmet</p></th>
                        <th><p>Adet</p></th>
                        <th><p>Birim Fiyat</p></th>
                        <th><p>Tutar</p></th>
                  </tr>
                    
                    <?php while($rows = mysqli_fetch_array($result)): ?>
                    <tr>
                      <td><?php echo $rows['product']; ?></td>
                      <td><?php echo $rows['quantity'] ?></td>
                      <td><?php echo $rows['unitprice']." ".$invoice['currency']; ?></td>
                      <td><?php echo $rows['price']." ".$invoice['currency']; ?></td>
                    </tr>
                    <?php endwhile; ?>
  </table>
  <p style="margin-top: 50px;margin-left: 15px;margin-bottom: 50px;"><?php echo $invoice['description'];?></p>
</div>

</body>
</html>