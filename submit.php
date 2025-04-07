<?php

//start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
      header("location: login.php");
      exit;
}

//get database config
require_once "config.php";
require_once "error_log.php";

//set invoice number to "null"
$no = 'null';
$comment = "";
$commentFinance = "";

//get invoice number from post of welcome.php

foreach ($_POST as $key=>$value) {
    $no = "$key";
}

if (isset($_POST['no'])) {
    $no = $_POST['no'];
}

if ($no == 'null') {
      header("location: welcome.php");
}

$sql = "SELECT mailgroup FROM users WHERE username=". "'" . $_SESSION["username"] . "'";
$result = mysqli_query($link, $sql);
$mailgroup = mysqli_fetch_array($result)['mailgroup'];

$sql = "SELECT * FROM invoices where no = " . "'" . $no . "'";
$result = mysqli_query($link, $sql);
$invoice =  mysqli_fetch_array($result);


if ($_SESSION["usertype"] != "superuser" && $_SESSION["usertype"] != "admin" && $invoice['assignee'] != $_SESSION["username"] && $invoice['assignee'] != $mailgroup) {
      header("location: 404.php");
      exit;
}


if ($invoice['state'] == 'İşlenmiş') {
    if ($_SESSION['usertype'] == "user") {
        $comment_err = "Bu fatura için bekleyen bir işlem yok";
        $scenario = "accessrestrict";

    } else if ($_SESSION['usertype'] == "admin") {
        $scenario = "accessrestrict";

    } else if ($_SESSION['usertype'] == "superuser") {
        // allow superuser to change invoice state - disable for now!
        $scenario = "superuser";
    }

} else if ($invoice['state'] == 'Onaylanmış' || $invoice['state'] == 'Reddedilmiş' || $invoice['state'] == 'Concur') {
    if ($_SESSION['usertype'] == "user") {
        $scenario = "assigneerevisit";
    } else if ($_SESSION['usertype'] == "admin" || $_SESSION['usertype'] == "superuser") {
        //change buttons to process
        $scenario = "admin";
    }
} else if ($invoice['state'] == 'Bekliyor') {
    $scenario = "assignee";
} else {
    $scenario = "error";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['process'])) {
        // process invoice    
        Process_invoice($link, $_POST["no"], "Atananın Yorumu: ".$invoice['comment']."\nFinans Yorumu: ".$_POST["commentFinance"]);
        $scenario = "success";
    } else if (isset($_POST['return'])) {
        Return_invoice($link, $_POST["no"], "Atananın Yorumu: ".$invoice['comment']."\nFinans Yorumu: ".$_POST["commentFinance"]);
        $scenario = "success";
    } else if (isset($_POST['approve'])) {
        
        if (isset($_POST['checkBox']) && $_POST['checkBox'] == "on") {
            Approve_Reject_invoice($link, $_POST["no"], "Concur", "Concur üzerinden işlenecek", $_POST["ponumber"]);
            $scenario = "success";
        } else if (empty(trim($_POST["ponumber"])) && empty(trim($_POST["comment"])) ) {
                $comment_err = "Lütfen bir açıklama veya PO/CO/RFA/SO numarası giriniz.";
        } else {
                // approve invoice
                Approve_Reject_invoice($link, $_POST["no"], "Onaylanmış", $_POST["comment"], $_POST["ponumber"]);
                $scenario = "success";
        }

    } else if (isset($_POST['reject'])) {
        
        if (empty(trim($_POST["ponumber"])) && empty(trim($_POST["comment"])) ) {
                $comment_err = "Lütfen bir açıklama veya PO/CO/RFA/SO numarası giriniz.";
        } else {
                // reject invoice
                Approve_Reject_invoice($link, $_POST["no"], "Reddedilmiş", $_POST["comment"], $_POST["ponumber"]);
                $scenario = "success";
        }
    }

}
 

function Approve_Reject_invoice($link, $no, $state, $comment, $po)
{
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state=?, comment=?, po_rfa=? WHERE no=?");
    mysqli_stmt_bind_param($stmt, "ssss", $state, $comment, $po, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    //echo $no."      ".$state."    ".$comment;
}

function Process_invoice($link, $no, $comment)
{
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state='İşlenmiş', comment=? WHERE no=?");
    mysqli_stmt_bind_param($stmt, "ss", $comment, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function Return_invoice($link, $no, $comment)
{
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 
    $stmt = mysqli_prepare($link, "UPDATE invoices SET state='Bekliyor', comment=? WHERE no=?");
    mysqli_stmt_bind_param($stmt, "ss", $comment, $no);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
    
    // Close connection
    //mysqli_close($link);

?>  



<!DOCTYPE html>
<html lang="en" style="background-image: url('background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Fatura Onay/Red</title>
    <link rel="stylesheet" href="styles.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 600px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
    <?php if ($scenario == "success") : ?>
        <p style="color:green;font-size:20px;">İşlem başarıyla tamamlandı.</p>
        <a class="btn btn-primary" href="welcome.php">Anasayfa</a>
    <?php else: ?>
        <h2 style="margin-bottom:20px;">Fatura Onaylama/İşleme</h2>
        <h5 style="margin-bottom:20px;">Fatura No: <?php echo $no; ?></h5>
        <h5 style="margin-bottom:20px;">Atanan: <?php echo $invoice['assignee']; ?></h5>
    <?php endif; ?>


<?php if ($scenario == "assignee") : ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($comment_err)) ? 'has-error' : ''; ?>">
                <p>Açıklama:</p>
                <textarea style="margin-bottom:20px;" type="text" name="comment" class="form-control" rows="5" maxlength="250" id="first"></textarea>
                <p>Önceki Yorumlar:</p>
                <textarea type="text" class="form-control" rows="5" style="margin-bottom:20px;" disabled><?php 
                    echo $invoice['comment'];
                    echo "\nPO/RFA No: ".$invoice['po_rfa'];
                ?></textarea>
                <p>Order RFA No:</p>
                <textarea style="width: 250px;margin-bottom:20px;" type="text" name="ponumber" class="form-control" rows="1" maxlength="28" id="second"></textarea>
                <span class="help-block"><?php
                if (isset($comment_err)) {
                    echo $comment_err;
                }
                ?></span>
                <input class="form-check-input" type="checkbox" role="switch" id="switch" name="checkBox" onchange='checkAction(this);'>
                <label class="form-check-label" for="flexSwitchCheckDefault">Concur üzerinden işlenecek</label>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" value="Onayla" name="approve" style="margin-right: 5px;">
                <input type="submit" class="btn btn-danger" value="Reddet" name="reject" id="reject" style="margin-right: 5px;">
                <input type=hidden name="no" value="<?php echo $no; ?>">
                <a class="btn btn-link" href="<?php echo $_SERVER['HTTP_REFERER']; ?>">İptal</a>
            </div>
    </form>
<?php endif; ?>



<?php if ($scenario == "assigneerevisit") : ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($comment_err)) ? 'has-error' : ''; ?>">
                <p>Açıklama:</p>
                <textarea style="margin-bottom:20px;" type="text" name="comment" class="form-control" rows="3" maxlength="250" id="first"><?php
                if ($invoice['state'] == "Concur") {
                    echo "Bu fatura Concur'a gönderilmiş.";
                } else {
                    echo "Bu fatura aşağıdaki yorum / PO ile ".strtolower($invoice['state']).":\n".$invoice['comment']."\nPO:".$invoice['po_rfa'];
                }
                ?></textarea>
                <p>Order RFA No:</p>
                <textarea style="width: 250px;margin-bottom:20px;" type="text" name="ponumber" class="form-control" rows="1" maxlength="28" id="second"></textarea>
                <span class="help-block"><?php echo $comment_err; ?></span>
                <input class="form-check-input" type="checkbox" role="switch" id="switch" onchange='checkAction(this);'>
                <label class="form-check-label" for="flexSwitchCheckDefault">Concur üzerinden işlenecek</label>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" value="Onayla" name="approve" style="margin-right: 5px;">
                <input type="submit" class="btn btn-danger" value="Reddet" name="reject" id="reject" style="margin-right: 5px;">
                <input type=hidden name="no" value="<?php echo $no; ?>">
                <a class="btn btn-link" href="<?php echo $_SERVER['HTTP_REFERER']; ?>">İptal</a>
            </div>
    </form>
<?php endif; ?>



<?php if ($scenario == "admin") : ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <textarea type="text" name="comment" class="form-control" rows="5" disabled><?php
                if ($invoice['state'] == "Concur") {
                    echo "Bu fatura Concur'a gönderilmiş.";
                } else {
                    echo "Bu fatura aşağıdaki yorum / PO ile ".strtolower($invoice['state']).":\n".$invoice['comment']."\nPO:".$invoice['po_rfa'];
                }
                ?></textarea>
                <p style="margin-top:15px;">Finans Yorum:</p>
                <textarea type="text" name="commentFinance" class="form-control" rows="5" style="margin-top:10px;"></textarea>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" value="Faturayı İşle" name="process" style="margin-right: 5px;">
                <input type="submit" class="btn btn-danger" value="Geri Gönder" name="return" style="margin-right: 5px;">
                <input type=hidden name="no" value="<?php echo $no; ?>">
                <a class="btn btn-link" href="<?php echo $_SERVER['HTTP_REFERER']; ?>">İptal</a>
            </div>
    </form>
<?php endif; ?>

<?php if ($scenario == "accessrestrict") : ?>
    <form>
            <div class="form-group">
                <textarea type="text" name="comment" class="form-control" rows="5" disabled><?php
                if ($invoice['state'] == "Concur") {
                    echo "Bu fatura Concur'a gönderilmiş.";
                } else {
                    echo "Bu fatura aşağıdaki yorum / PO ile ".strtolower($invoice['state']).":\n".$invoice['comment']."\nPO:".$invoice['po_rfa'];
                }
                ?></textarea>
            </div>
            <div class="form-group">
                <input type=hidden name="no" value="<?php echo $no; ?>">
                <a class="btn btn-link" href="<?php echo $_SERVER['HTTP_REFERER']; ?>">İptal</a>
            </div>
    </form>
<?php endif; ?>

<?php if ($scenario == "superuser") : ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <textarea type="text" name="comment" class="form-control" rows="5" disabled><?php
                if ($invoice['state'] == "Concur") {
                    echo "Bu fatura Concur'a gönderilmiş.";
                } else {
                    echo "Bu fatura aşağıdaki yorum / PO ile ".strtolower($invoice['state']).":\n".$invoice['comment']."\nPO:".$invoice['po_rfa'];
                }
                ?></textarea>
            </div>
            <div class="form-group">
                <input type=hidden name="no" value="<?php echo $no; ?>">
                <a class="btn btn-link" href="<?php echo $_SERVER['HTTP_REFERER']; ?>">İptal</a>
            </div>
    </form>
<?php endif; ?>

<?php if (!isset($scenario) || $scenario == "error") : ?>
<p style="color:red;font-size:20px;">Error: Couldn't determine the scenario. Please contact administrator.</p>
<br><br>
<a class="btn btn-link" href="<?php
echo $_SERVER['HTTP_REFERER']; ?>">İptal</a><?php
echo "\nInvoice no: ".$no;?>
<?php endif; ?>
</div> 
    
<script>

window.onload = function() {
    var checkBox = document.getElementById("checkBox");
    checkAction(checkBox); // Call the function to set the initial state
}

function checkAction(checkBox) {
    if (checkBox.checked == true) {
        document.getElementById("first").value = "Concur üzerinden işlenecek";
        document.getElementById("first").readOnly = true;
        document.getElementById("second").readOnly = true;
        document.getElementById("reject").disabled = true;
    }else {
        document.getElementById("first").readOnly = false;
        document.getElementById("second").readOnly = false;
        document.getElementById("reject").disabled = false;
   }
}

</script>
</body>
</html>