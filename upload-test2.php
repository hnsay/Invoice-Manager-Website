<?php

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location:login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
	exit;
}

if($_SESSION["usertype"] != "superuser" && $_SESSION["usertype"] != "admin" ) {
    header("location: 404.php");
    exit;
}
 
// Include config file
require_once "config.php";
require_once "error_log.php";

$textArea = "";


$affectedRows = 0;

function Format_description($string){

	//
	//$clr = str_replace("\nNOBR","",$clr);
	//$clr = strip_tags($clr);
	$clr = str_replace("*UST*NOBR<br />", "", $string);
	//$clr = strip_tags($clr);
	//$clr = nl2br($clr);
	$clr = str_replace("*UST*", "", $clr);
	$clr = str_replace("NOBR<br />", "", $clr);
	$clr = str_replace("*UST*NOBR", "", $clr);
	$clr = str_replace("NOBR", "", $clr);
	$clr = str_replace("<br />\n: ", " : ", $clr);
	return $clr;
	//return strip_tags($clr); Table
	//return htmlspecialchars($clr);
  
  //return nl2br(strip_tags(str_replace("*UST*NOBR","",str_replace("\nNOBR","",$string))));
  
  //nl2br(strip_tags(str_replace("*UST*NOBR","",str_replace("\nNOBR","",$invoice['description']))));
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

<?php include 'navbar.php'; ?>

<body>
<div class="wrapper">
	<h2>XML Dosya Yükleme</h2>
	<form action="upload-test2.php" method="post" enctype="multipart/form-data">
		<div class="form-group" id = "div1" <?php if (isset($_POST["xmlFiles"])){echo "hidden";}?>>
			<input id="selector" name="xmlFiles[]" type="file" multiple value="" class="form-control">
		</div>
		<div class="form-group" id = "div2" <?php if (isset($_POST["xmlFiles"])){echo "hidden";}?>>
			<input name="xmlFiles[]" type="submit" value="Seçilenleri Yükle" class="btn btn-success" id="button2">
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
    var files = document.getElementById('selector').files;

    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        var reader = new FileReader();

        reader.onload = function(event) {
            var xmlString = event.target.result;
            var parser = new DOMParser();
            var xmlDoc = parser.parseFromString(xmlString, 'text/xml');
            var ns1, ns2;
            var description = '';
            var partyName;

            // Your namespace detection logic
            // Example:
            var namespaces = xmlDoc.documentElement.namespaceURI;

            // Extract necessary information
            // Example:
			var prodNames = xmlDoc.querySelectorAll('Note');
			
			
            for (var j = 0; j < prodNames.length; j++) {
                description += prodNames[j].textContent + '\n';
            }

            var accountingSupplierParty = xmlDoc.querySelector('AccountingSupplierParty');
            if (accountingSupplierParty) {
                // Extract party name
                // Example:
                var partyNameElement = accountingSupplierParty.querySelector('PartyName Name');
                if (partyNameElement) {
                    partyName = partyNameElement.textContent;
                } else {
                    // If Name element not found, construct from Person elements
                    var firstName = accountingSupplierParty.querySelector('Person FirstName').textContent;
                    var familyName = accountingSupplierParty.querySelector('Person FamilyName').textContent;
                    partyName = firstName + ' ' + familyName + ' (Şahıs Şirketi)';
                }

                // Extract other necessary fields
                // Example:
                var ID = xmlDoc.querySelector('ID').textContent;
                var issueDate = xmlDoc.querySelector('IssueDate').textContent;
                var payableAmount = xmlDoc.querySelector('LegalMonetaryTotal PayableAmount').textContent;
                var documentCurrencyCode = xmlDoc.querySelector('DocumentCurrencyCode').textContent;
                // Upload relevant data to server
                // Example:
                //Upload_invoice(partyName, ID, issueDate, payableAmount, documentCurrencyCode, description);
				console.log(partyName);
				console.log(ID);
				console.log(issueDate);
				console.log(payableAmount);
				console.log(documentCurrencyCode);
				console.log(description);
            } else {
                console.log('AccountingSupplierParty not found in XML.');
            }
        };

        reader.readAsText(file);
    }
}
}
</script>
</body>
</html>