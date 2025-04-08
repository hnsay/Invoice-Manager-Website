<?php
/**
 * Upload XML invoices.
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
 
// Include config file
require_once "/config/config.php";
require_once "error_log.php";

$textArea = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $inputData = json_decode(file_get_contents("php://input"), true);

    if (is_array($inputData)) {
        foreach ($inputData as $row) {
            Upload_invoice($link, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
            Clear_invoicelines($link, $row[1]);
            foreach ($row[6] as $line) {
                Upload_invoiceline($link, $line[0], $line[1], $line[2], $line[3], $line[4]);
            }
        }
        $textArea = "Faturalar Yüklendi!";
    }
}

function Upload_invoice($link, $supplier, $no, $date, $amount, $currency, $description)
{
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 
    $stmt = mysqli_prepare($link, "INSERT IGNORE INTO invoices(supplier,no,state,date,amount,currency,description) VALUES (?,?,'Bekliyor',?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $supplier, $no, $date, $amount, $currency, $description);
    //mysqli_stmt_execute($stmt);
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Insert failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

function Clear_invoicelines($link, $no)
{
    $stmt = mysqli_prepare($link, "DELETE FROM invoicelines WHERE no=?");
    mysqli_stmt_bind_param($stmt, "s", $no);
    //mysqli_stmt_execute($stmt);
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Insert failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

function Upload_invoiceline($link, $no, $product, $quantity, $unitprice, $price)
{
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 
    $stmt = mysqli_prepare($link, "INSERT IGNORE INTO invoicelines(no,product,quantity,unitprice,price) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "sssss", $no, $product, $quantity, $unitprice, $price);
    //mysqli_stmt_execute($stmt);
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Insert failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en" style="background-image: url('background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Fatura Yükleme</title>
    <link rel="stylesheet" href="styles.css">
    <style type="text/css">
        body{ font: 12px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>

<?php require 'navbar.php'; ?>

<body>
<div class="wrapper">
    <h2>XML Dosya Yükleme</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data" id=myForm>
        <div class="form-group" id = "div1" <?php
        if (isset($_POST["xmlFiles"])) {
            echo "hidden";
        }
        ?>><input id="selector" name="xmlFiles[]" type="file" multiple value="" class="form-control">
        </div>
        <div class="form-group" id = "div2" <?php
        if (isset($_POST["xmlFiles"])) {
            echo "hidden";
        }
        ?>><input name="xmlFiles[]" type="submit" value="Seçilenleri Yükle" class="btn btn-success" id="button2">
        </div>
        <div class="form-group">            
            <textarea style="white-space: pre-wrap;" class="form-control" id="textarea" rows="10" disabled><?php echo $textArea;?></textarea>
          </div>
    </form>
</div>
<br><br><br><br>
<script>

document.getElementById("button2").onclick = function() {loading()};

function loading() {
  document.getElementById("div1").style.display = "none";
  document.getElementById("div2").style.display = "none";
  document.getElementById("textarea").value = "Lütfen bekleyiniz, faturalar yükleniyor ...";
  printInvoice();
}

function printInvoice() {
    if (typeof FormData !== 'undefined' && document.getElementById('selector').files.length > 0) {
        
        var array = [];
        var files = document.getElementById('selector').files;
        var filesProcessed = 0;

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();

            reader.onload = function(event) {
                var xmlString = event.target.result;
                var parser = new DOMParser();
                var xmlDoc = parser.parseFromString(xmlString, 'text/xml');
                var description = '';
                var supplier;
                var namespaces = xmlDoc.documentElement.namespaceURI;
                var prodNames = xmlDoc.querySelectorAll('Note');
            
            
                for (var j = 0; j < prodNames.length; j++) {
                    description += prodNames[j].textContent + '\n';
                }

                if (xmlDoc.querySelector('AccountingSupplierParty')) {
                    if (xmlDoc.querySelector('AccountingSupplierParty').querySelector('PartyName Name')) {
                        supplier = xmlDoc.querySelector('AccountingSupplierParty').querySelector('PartyName Name').textContent;
                    } else {
                        var firstName = xmlDoc.querySelector('AccountingSupplierParty').querySelector('Person FirstName').textContent;
                        var familyName = xmlDoc.querySelector('AccountingSupplierParty').querySelector('Person FamilyName').textContent;
                        supplier = firstName + ' ' + familyName + ' (Şahıs Şirketi)';
                    }

                    var invoiceNo = xmlDoc.querySelector('ID').textContent;
                    var issueDate = xmlDoc.querySelector('IssueDate').textContent;
                    var amount = xmlDoc.querySelector('LegalMonetaryTotal PayableAmount').textContent;
                    var currency = xmlDoc.querySelector('DocumentCurrencyCode').textContent;

                    var invoiceLines = xmlDoc.querySelectorAll('InvoiceLine');

                    let invoiceLineArray = [];

                    for (var j = 0; j < invoiceLines.length; j++) {
                        var productName = invoiceLines[j].querySelector('Item Name').textContent;
                        var quantity = invoiceLines[j].querySelector('InvoicedQuantity').textContent;
                        var unitPrice = invoiceLines[j].querySelector('Price PriceAmount').textContent;
                        var price = invoiceLines[j].querySelector('LineExtensionAmount').textContent;
                        invoiceLineArray.push([invoiceNo, productName, quantity, unitPrice, price]);
                    }

                    array.push([supplier, invoiceNo, issueDate, amount, currency, formatDescription(description), invoiceLineArray]);

                } else {
                    console.log('Supplier name is not found');
                }

                filesProcessed++;
                if (filesProcessed === files.length) {
                    sendData(array);
                }
            };
            reader.readAsText(file);
        }
    }
}

function sendData(data) {
    fetch('upload.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(data),
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('myForm').addEventListener('submit', function(event) {
        event.preventDefault();
        //alert('Form submission prevented!');
    });
});


function formatDescription(string) {
    let clr = string.replace(/\*UST\*NOBR<br \/>/g, "")
                    .replace(/\*UST\*/g, "")
                    .replace(/NOBR<br \/>/g, "")
                    .replace(/\*UST\*NOBR/g, "")
                    .replace(/NOBR/g, "")
                    .replace(/<br \/>\\n: /g, " : ");
    return clr;
}
</script>
</body>
</html>