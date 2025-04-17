<?php
/**
 * Invoice SQL functions.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <say@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
// SQL CLOSE?
require_once SESSION_HELPER;

function getAllInvoices($link) {
    $sql = "SELECT * FROM invoices";
    $result = mysqli_query($link, $sql);
    return mysqli_fetch_array($result);
    mysqli_close($link);
}

function getInvoice($link, $no) {
    $stmt = mysqli_prepare($link, "SELECT * FROM invoices WHERE no = ?");
    mysqli_stmt_bind_param($stmt, "s", $no);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

function getInvoiceLines($link, $no) {
    $stmt = mysqli_prepare($link, "SELECT * FROM invoicelines WHERE no = ?");
    mysqli_stmt_bind_param($stmt, "s", $no);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $array = [];
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $array[] = $row;
    }
    return $array;
    mysqli_stmt_close($stmt);
}

function getRules($link) 
{
    $sql = "SELECT * FROM rules ORDER BY supplier ASC";
    $result = mysqli_query($link, $sql);
    $array = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $array[] = $row;
    }
    return $array;
}

function getSuppliers($link) 
{
    $sql = "SELECT DISTINCT supplier FROM invoices ORDER BY supplier ASC";
    $result = mysqli_query($link, $sql);
    $array = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $array[] = $row;
    }
    return $array;
}

function createRule($link, $supplier, $username)
{
    $sql = "INSERT INTO rules (supplier, username) VALUES (?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $param_supplier, $param_username);
    $param_supplier = $supplier;
    $param_username = $username;
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    }
    return false;
}

function updateRule($link, $supplier, $username)
{
    $sql = "UPDATE rules SET username = ? WHERE supplier = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $param_supplier, $param_username);
    $param_supplier = $username;
    $param_username = $supplier;
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    }
    return false;
}

function deleteRule($link, $supplier)
{
    $sql = "DELETE from rules where supplier=?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $first);
    $first = $supplier;
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    }
    return false;
}

function findInArray($rules, $column , $supplier)
{
    foreach ($rules as $row) {
        if ($row[$column] === $supplier) {
            return true;
        }
    }
    return false;
}

function getAssignedInvoices($link, $username, $mailgroup = null) {
    if ($mailgroup === null) {
        $stmt = mysqli_prepare($link, "SELECT * FROM invoices WHERE state='Bekliyor' AND assignee=?");
        mysqli_stmt_bind_param($stmt, "s", $username);
    } else {
        $stmt = mysqli_prepare($link, "SELECT * FROM invoices WHERE state='Bekliyor' AND (assignee=? OR assignee=?)");
        mysqli_stmt_bind_param($stmt, "ss", $username, $mailgroup);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $array = [];
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $array[] = $row;
    }
    return $array;
    mysqli_stmt_close($stmt);
}

function verifyInvoice($link, $no)
{    
    $stmt = mysqli_prepare($link, "SELECT assignee, state FROM invoices WHERE no = ?");
    mysqli_stmt_bind_param($stmt, "s", $no);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $invoice = mysqli_fetch_array($result, MYSQLI_ASSOC);
    
    $mailgroup = getMailGroup($link, $_SESSION["username"]);
    
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

function invoiceApproveRejectBulk($link, $no, $state, $comment, $po)
{
    $detail_check = verifyInvoice($link, $no);
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

function processInvoiceAdmin($link, $no, $comment, $comment2)
{
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state='İşlenmiş', comment=CONCAT(?, comment, ?) WHERE no=?");
    mysqli_stmt_bind_param($stmt, "sss", $comment, $comment2, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function returnInvoiceAdmin($link, $no, $comment, $comment2)
{
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state='Bekliyor', comment=CONCAT(?, comment, ?) WHERE no=?");
    mysqli_stmt_bind_param($stmt, "sss", $comment, $comment2, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function invoiceApproveReject($link, $no, $state, $comment, $po)
{
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state=?, comment=?, po_rfa=? WHERE no=?");
    mysqli_stmt_bind_param($stmt, "ssss", $state, $comment, $po, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function processInvoice($link, $no, $comment)
{
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state='İşlenmiş', comment=? WHERE no=?");
    mysqli_stmt_bind_param($stmt, "ss", $comment, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function returnInvoice($link, $no, $comment)
{
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state='Bekliyor', comment=? WHERE no=?");
    mysqli_stmt_bind_param($stmt, "ss", $comment, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function uploadInvoice($link, $supplier, $no, $date, $amount, $currency, $description)
{
    $stmt = mysqli_prepare($link, "INSERT IGNORE INTO invoices(supplier,no,state,date,amount,currency,description) VALUES (?,?,'Bekliyor',?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $supplier, $no, $date, $amount, $currency, $description);
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Insert failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

function clearInvoiceLines($link, $no)
{
    $stmt = mysqli_prepare($link, "DELETE FROM invoicelines WHERE no=?");
    mysqli_stmt_bind_param($stmt, "s", $no);
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Insert failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

function uploadInvoiceLine($link, $no, $product, $quantity, $unitprice, $price)
{
    $stmt = mysqli_prepare($link, "INSERT IGNORE INTO invoicelines(no,product,quantity,unitprice,price) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "sssss", $no, $product, $quantity, $unitprice, $price);
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Insert failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

function assignCommentInvoice($link, $assignee, $no, $comment)
{
    if ($comment == "remove") {
        $stmt = mysqli_prepare($link, "UPDATE invoices SET assignee=?, comment = NULL WHERE no=?");
        mysqli_stmt_bind_param($stmt, "ss", $assignee, $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $stmt = mysqli_prepare($link, "UPDATE invoices SET assignee=? WHERE no=?");    
        mysqli_stmt_bind_param($stmt, "ss", $assignee, $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function updateAssignInvoice($link, $assignee, $state, $no, $comment)
{
    if ($comment == "remove") {
        $stmt = mysqli_prepare($link, "UPDATE invoices SET assignee=?, state=?, comment = NULL WHERE no=?");
        mysqli_stmt_bind_param($stmt, "sss", $assignee, $state, $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $stmt = mysqli_prepare($link, "UPDATE invoices SET assignee=?, state=? WHERE no=?");
        mysqli_stmt_bind_param($stmt, "sss", $assignee, $state, $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function updateAssignInvoiceConcur($link, $assignee, $no)
{
    $stmt = mysqli_prepare($link, "UPDATE invoices SET assignee=?, state='Concur', comment='Concur üzerinden işlenecek' WHERE no=?");
    mysqli_stmt_bind_param($stmt, "ss", $assignee, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


function assignInvoice($link, $no, $comment)
{
    if ($comment == "remove") {
        $stmt = mysqli_prepare($link, "UPDATE invoices SET comment = NULL WHERE no=?");
        mysqli_stmt_bind_param($stmt, "s", $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function updateInvoice($link, $state, $no, $comment)
{
    if ($comment == "remove") {
        $stmt = mysqli_prepare($link, "UPDATE invoices SET state=?, comment = NULL WHERE no=?");
        mysqli_stmt_bind_param($stmt, "ss", $state, $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $stmt = mysqli_prepare($link, "UPDATE invoices SET state=? WHERE no=?");
        mysqli_stmt_bind_param($stmt, "ss", $state, $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function updateInvoiceConcur($link, $no)
{
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state='Concur', comment='Concur üzerinden işlenecek' WHERE no=?");
    mysqli_stmt_bind_param($stmt, "s", $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

?>