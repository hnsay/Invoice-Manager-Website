<?php
/**
 * Page allowing the user to approve invoices in bulk.
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
require_once MODEL_USER;

//check user's mailgroup
$mailgroup = getMailGroup($link, $_SESSION["username"]);

$invoices = getAssignedInvoices($link, $_SESSION["username"], $mailgroup);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Toplu Fatura İşleme</title>
    <link rel="icon" type="image/x-icon" href="/public/icons/favicon.ico">


    <script src="/public/Datatables/datatables.min.js"></script>
    <script src="/public/Datatables/moment.min.js"></script>
    <script src="/public/Datatables/dataTables.checkboxes.min.js"></script>
    <script src="/public/Datatables/jquery.dataTables.colResize.js"></script>
    <script src="/public/Datatables/select2.min.js"></script>
    <link rel="stylesheet" href="/public/Datatables/datatables.css"/>
    <link rel="stylesheet" href="/public/css/jquery.dataTables.css">
    <link rel="stylesheet" href="/public/css/awesome-bootstrap-checkbox.css">
    <link rel="stylesheet" href="/public/css/styles.css">
    <link rel="stylesheet" type="text/css" href="/public/css/select2.min.css">

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
<form action="approve_bulk_submit.php" method="post" id="submitBulkForm" onsubmit="sendData()">
            <div class="form-group <?php echo (!empty($comment_err)) ? 'has-error' : ''; ?>">
                <p>Açıklama:</p>
                <textarea style="margin-bottom:20px;" type="text" name="comment" class="form-control" rows="3" maxlength="250" id="first"></textarea>
                <p>Order RFA No:</p>
                <textarea style="width: 250px;margin-bottom:20px;" type="text" name="ponumber" class="form-control" rows="1" maxlength="28" id="second"></textarea>
                <span class="help-block" id="helpBlock"></span>
                <input class="form-check-input" type="checkbox" role="switch" id="checkBox" name="checkBox" onchange='checkAction(this);'>
                <label class="form-check-label" for="flexSwitchCheckDefault">Concur üzerinden işlenecek</label>
            </div>
            <div class="form-group">
                <input form="submitBulkForm" type="submit" class="btn btn-success" value="Onayla" name="approve" style="margin-right: 5px;" onclick="return approveButton()">
                <input form="submitBulkForm" type="submit" class="btn btn-danger" value="Reddet" name="reject" style="margin-right: 5px;" onclick="return rejectButton()">
            </div>
</form>
</div>
<div style="padding-left: 50px;padding-right: 50px;">
  <table class="table responsive" id="sorTable">
    <thead>
    <tr>
      <th data-priority="8" scope="col" style="width: 1%;">#</th>
            <th data-priority="5" scope="col" style="width: 25%;">Tedarikçi</th>
            <th data-priority="1" scope="col" style="width: 15%;">Fatura No</th>
            <th data-priority="2" scope="col" style="width: 1%;">Durum</th>
            <th data-priority="4" scope="col" style="width: 10%;">Tarih</th>
            <th data-priority="6" scope="col" style="width: 1%;">Tutar</th>
      <th data-priority="7" scope="col" style="width: 1%;">PB</th>
      <th scope="col" style="width: 1%;">Açıklama</th>
      <th data-priority="3" scope="col" style="width: 1%;">Atanan</th>
    </tr>
  </thead>
  <tbody>
        <?php foreach ($invoices as $invoice): ?>
    <tr>
      <td data-table-header="#"></td>
      <td data-table-header="Tedarikçi"><?php echo $invoice['supplier']; ?></td>
      <td data-table-header="Fatura No">
                <?php echo '<a href="invoice.php?'.$invoice['no'].'=" target="_blank">'.$invoice['no'].'</a>'; ?>
            </td>
      <td data-table-header="Durum"><?php echo $invoice['state']; ?></td>
      <td data-table-header="Tarih"><?php echo $invoice['date']; ?></td>
      <td data-table-header="Tutar"><?php echo $invoice['amount']; ?></td>
      <td data-table-header="PB"><?php echo $invoice['currency']; ?></td>
      <td data-table-header="Açıklama"><?php 
                    echo strip_tags($invoice['description']);?>
      </td>
      <td data-table-header="Atanan"><?php 
                    echo $invoice['assignee'];?>
      </td>
    </tr>

        <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require TABLE_APPROVE_HELPER; ?>
</body>
</html>
