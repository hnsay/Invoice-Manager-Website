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
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
require_once SESSION_HELPER;
require_once MODEL_INVOICE;
protectPage(['superuser'], ['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $inputData = json_decode(file_get_contents("php://input"), true);

    if (is_array($inputData)) {
        foreach ($inputData as $row) {
            uploadInvoice($link, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
            clearInvoiceLines($link, $row[1]);
            foreach ($row[6] as $line) {
                uploadInvoiceLine($link, $line[0], $line[1], $line[2], $line[3], $line[4]);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" style="background-image: url('/public/images/background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Fatura Yükleme</title>
    <link rel="stylesheet" href="/public/css/styles.css">
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
        <div class="form-group" id = "div2">
            <input name="xmlFiles[]" type="submit" value="Seçilenleri Yükle" class="btn btn-success" id="button2">
        </div>
        <div class="form-group">            
            <textarea style="white-space: pre-wrap;" class="form-control" id="textarea" rows="10" disabled></textarea>
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
    return new Promise((resolve, reject) => {
        var array = [];

        if (typeof FormData !== 'undefined' && document.getElementById('selector').files.length > 0) {
            var files = document.getElementById('selector').files;
            var filesProcessed = 0;

            for (var i = 0; i < files.length; i++) {
                (function(file) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        var xmlString = event.target.result;
                        var parser = new DOMParser();
                        var xmlDoc = parser.parseFromString(xmlString, 'text/xml');
                        var description = '';
                        var supplier;
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
                        }

                        filesProcessed++;
                        if (filesProcessed === files.length) {
                            resolve(array);
                        }
                    };

                    reader.onerror = function() {
                        reject('Error reading file: ' + file.name);
                    };

                    reader.readAsText(file);
                })(files[i]);
            }
        } else {
            resolve(array); // No files to process
        }
    });
}


document.getElementById('myForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const textarea = document.getElementById('textarea');

    try {
        const xmlData = await printInvoice();
        const response = await fetch('upload.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(xmlData)
        });

        if (response.ok) {
            textarea.value = 'Faturalar Yüklendi!';
        } else {
            textarea.value = 'Yükleme başarısız.';
        }
    } catch (err) {
        textarea.value = 'Hata oluştu: ' + err;
    }
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