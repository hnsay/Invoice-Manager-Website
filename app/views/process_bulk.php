<?php
/**
 * Page allowing the finance admins to process invoices pending their action in bulk.
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

protectPage(['superuser']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Toplu Fatura İşleme</title>

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
<h1>Toplu Fatura İşleme</h1> <br>
<div class="wrapper" style="padding-left: 50px;">
<form action="process_bulk_submit.php" method="post" id="submitBulkForm" onsubmit="sendData()">
            <div class="form-group <?php echo (!empty($comment_err)) ? 'has-error' : ''; ?>">
                <p>Finans Yorum:</p>
                <textarea style="margin-bottom:20px;" type="text" name="commentFinance" class="form-control" rows="3" maxlength="250" id="textArea"></textarea>
                <span class="help-block" id="helpBlock"></span>
            </div>
            <div class="form-group">
                <input form="submitBulkForm" type="submit" class="btn btn-success" value="Faturaları İşle" name="process" style="margin-right: 5px;" onclick="return approveButton()">
                <input form="submitBulkForm" type="submit" class="btn btn-danger" value="Geri Gönder" name="reject" style="margin-right: 5px;" onclick="return rejectButton()">
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
    <tr>
      <td data-table-header="#"></td>
      <td data-table-header="Tedarikçi"></td>
      <td data-table-header="Fatura No"></td>
      <td data-table-header="Durum"></td>
      <td data-table-header="Tarih"></td>
      <td data-table-header="Tutar"></td>
      <td data-table-header="PB"></td>
            <th scope="col" style="width: 1%;">Açıklama</th>
      <td data-table-header="Atanan"></td>
    </tr>
  </tbody>
</table>
</div>
<?php require TABLE_PROCESS_LITE_HELPER ?>
</body>
</html>