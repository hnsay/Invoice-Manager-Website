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
            Assign_Invoice_overload($link, $no, $comment);
            //$message = "The action has been completed successfully";
        } else {
            Assign_invoice($link, $user, $no, $comment);
            //$message = "The action has been completed successfully";
        }
    }

} else if ($_POST['state'] == "Concur") {

    foreach ($_POST['array'] as $no) {
        if ($user == "Default") {
            Change_Invoice_Concur_overload($link, $no);
            //$message = "The action has been completed successfully";
        } else {
            Change_Invoice_concur($link, $user, $no);
            //$message = "The action has been completed successfully";
        }
    }

} else {
    $state = $_POST['state'];
    foreach ($_POST['array'] as $no) {
        if ($user == "Default") {
            Change_Invoice_overload($link, $state, $no, $comment);
            //$message = "The action has been completed successfully";
        } else {
            Change_invoice($link, $user, $state, $no, $comment);
            //$message = "The action has been completed successfully";
        }        
    }
}


echo "<script>window.close();</script>";

// DEBUGGING
//echo "user: ".$user."<br>";
//echo "no: ".$no."<br>";
//echo "state: ".$state."<br>";



function Assign_invoice($link, $assignee, $no, $comment)
{
    if ($comment == "remove") {
        //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        // config require once here was protected from replace operation 
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

function Change_invoice($link, $assignee, $state, $no, $comment)
{
    if ($comment == "remove") {
        $stmt = mysqli_prepare($link, "UPDATE invoices SET assignee=?, state=?, comment = NULL WHERE no=?");
        mysqli_stmt_bind_param($stmt, "sss", $assignee, $state, $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        // config require once here was protected from replace operation 
        $stmt = mysqli_prepare($link, "UPDATE invoices SET assignee=?, state=? WHERE no=?");
        mysqli_stmt_bind_param($stmt, "sss", $assignee, $state, $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function Change_Invoice_concur($link, $assignee, $no)
{
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 
    $stmt = mysqli_prepare($link, "UPDATE invoices SET assignee=?, state='Concur', comment='Concur üzerinden işlenecek' WHERE no=?");
    mysqli_stmt_bind_param($stmt, "ss", $assignee, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


function Assign_Invoice_overload($link, $no, $comment)
{
    if ($comment == "remove") {
        //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        // config require once here was protected from replace operation 
        $stmt = mysqli_prepare($link, "UPDATE invoices SET comment = NULL WHERE no=?");
        mysqli_stmt_bind_param($stmt, "s", $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function Change_Invoice_overload($link, $state, $no, $comment)
{
    if ($comment == "remove") {
        $stmt = mysqli_prepare($link, "UPDATE invoices SET state=?, comment = NULL WHERE no=?");
        mysqli_stmt_bind_param($stmt, "ss", $state, $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        // config require once here was protected from replace operation 
        $stmt = mysqli_prepare($link, "UPDATE invoices SET state=? WHERE no=?");
        mysqli_stmt_bind_param($stmt, "ss", $state, $no);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function Change_Invoice_Concur_overload($link, $no)
{
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state='Concur', comment='Concur üzerinden işlenecek' WHERE no=?");
    mysqli_stmt_bind_param($stmt, "s", $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

?>