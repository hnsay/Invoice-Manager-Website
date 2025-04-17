<?php
/**
 * Process actions sent by allinvoices.php pending.php and pending_finance.php through reference by table_process.php.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <say@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
require_once SESSION_HELPER;
require_once MODEL_INVOICE;
require_once MODEL_USER;

protectPage(['superuser'], ['admin']);


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: welcome.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";

if ($_POST['username'] == "Varsayılan") {
    $user = "Default";
} else if ($_POST['username'] == "Atamayı Kaldır") {
    $user = null;
} else {
    $user = $_POST['username'];
}

if ($_POST['comment'] == "Yorumları Kaldır") {
    $comment = "remove";
} else {
    $comment= "";
}

if ($_POST['state'] == "Varsayılan" || $_POST['state'] == null) {

    foreach ($_POST['array'] as $no) {
        if ($user == "Default") {
            assignInvoice($link, $no, $comment);
        } else {
            assignCommentInvoice($link, $user, $no, $comment);
        }
    }

} else if ($_POST['state'] == "Concur") {

    foreach ($_POST['array'] as $no) {
        if ($user == "Default") {
            updateInvoiceConcur($link, $no);
        } else {
            updateAssignInvoiceConcur($link, $user, $no);
        }
    }

} else {
    $state = $_POST['state'];
    foreach ($_POST['array'] as $no) {
        if ($user == "Default") {
            updateInvoice($link, $state, $no, $comment);
        } else {
            updateAssignInvoice($link, $user, $state, $no, $comment);
        }        
    }
}

echo "<script>window.close();</script>";
?>