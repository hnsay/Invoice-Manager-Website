<?php

// Initialize the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
      header("Location:app/views/login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
      exit;
}

if ($_SESSION["usertype"] != "superuser" && $_SESSION["usertype"] != "admin" ) {
      header("location: app/views/404.php");
      exit;
}
 
// Include config file
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";

$textArea = "";

if (isset($_POST["xmlFiles"])) {
    //print_r($_FILES);
        foreach( $_FILES[ 'xmlFiles' ][ 'tmp_name' ] as $location )
            {
                $xml = simplexml_load_file($location);
                $description = '';
                /* Deprecated registers
                $xml->registerXPathNamespace('ubltr', 'urn:oasis:names:specification:ubl:schema:xsd:TurkishCustomizationExtensionComponents');
                $xml->registerXPathNamespace('qdt', 'urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2');
                $xml->registerXPathNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
                $xml->registerXPathNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
                $xml->registerXPathNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
                $xml->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance');
                $xml->registerXPathNamespace('ccts', 'urn:un:unece:uncefact:documentation:2');
                $xml->registerXPathNamespace('ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
                $xml->registerXPathNamespace('xades', 'http://uri.etsi.org/01903/v1.3.2#');
                $xml->registerXPathNamespace('udt', 'urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2');
                */
    
                /* Deprecated function
                Upload_invoice($link,
                    $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0],
                    $xml->children('cbc', true)->ID,
                    $xml->children('cbc', true)->IssueDate,
                    $xml->xpath('//cbc:TaxInclusiveAmount')[0],
                    $xml->xpath('//cbc:DocumentCurrencyCode')[0],
                    $xml->xpath('//cbc:Note')[0]);

                */

                foreach ($xml->children('cbc', true)->Note as $note){
                    if (strpos($note, '#!#') === false ){
                    $description .= $note."\n";
                    }
                }
                
                echo "Test2<br>";
                
                echo $xml->children('cac', true)->AccountingSupplierParty->children('cac', true)->Party->children('cac', true)->Person->children('cbc', true)->FirstName.
                    " ".$xml->children('cac', true)->AccountingSupplierParty->children('cac', true)->Party->children('cac', true)->Person->children('cbc', true)->FamilyName.
                    " (Şahıs Firması)";
                
                if ($xml->children('cac', true)->AccountingSupplierParty->children('cac', true)->Party->children('cac', true)->PartyName->children('cbc', true)->Name == null) {

                }
                else
                {
                    echo "not null";
                }
                echo "<br>";
                echo $xml->children('cbc', true)->ID;
                echo "<br>";
                echo $xml->children('cbc', true)->IssueDate;
                echo "<br>";
                echo $xml->children('cac', true)->LegalMonetaryTotal->children('cbc', true)->PayableAmount;
                echo "<br>";
                echo $xml->children('cbc', true)->DocumentCurrencyCode;
                echo "<br>";    
                
                Upload_invoice($link,
                $xml->children('cac', true)->AccountingSupplierParty->children('cac', true)->Party->children('cac', true)->PartyName->children('cbc', true)->Name,
                $xml->children('cbc', true)->ID,
                $xml->children('cbc', true)->IssueDate,
                $xml->children('cac', true)->LegalMonetaryTotal->children('cbc', true)->PayableAmount,
                $xml->children('cbc', true)->DocumentCurrencyCode,
                //$xml->xpath('//cbc:Note')[0]);
                $description);



                foreach ($xml->children('cac', true)->InvoiceLine as $invoiceLine)
                {
                    Upload_invoiceline($link,
                    $xml->children('cbc', true)->ID,
                    $invoiceLine->children('cac', true)->Item->children('cbc', true)->Name,
                    $invoiceLine->children('cbc', true)->InvoicedQuantity,
                    $invoiceLine->children('cac', true)->Price->children('cbc', true)->PriceAmount,
                    $invoiceLine->children('cbc', true)->LineExtensionAmount);               
                }

                //$textArea = $textArea.$xml->xpath('//cbc:ID')[0]." başarıyla yüklendi. \n";
            }
            $textArea = "Faturalar başarıyla yüklendi";

mysqli_close($link);            
}

$affectedRows = 0;

function Upload_invoice($link,$supplier,$no,$date,$amount,$currency,$description){
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 

    //$stmt = mysqli_prepare($link, "INSERT INTO invoices(supplier,no,state,date,amount,currency,description) VALUES (?,?,'Bekliyor',?,?,?,?)");
    echo "Test<br>";
    echo $supplier."<br>";
    echo $no."<br>";
    echo $date."<br>";
    echo $amount."<br>";
    echo $currency."<br>";
    echo $description."<br>";
}

function Upload_invoiceline($link,$no,$product,$quantity,$unitprice,$price){
    //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // config require once here was protected from replace operation 

    echo "INSERT INTO invoicelines(no,product,quantity,unitprice,price) VALUES (".$no.",".$product.",".$quantity.",".$unitprice.",".$price.")";
}

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


<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="welcome.php"><img src="favicon.ico"></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li><a href="welcome.php">Anasayfa</a></li>
        <li><a href="app/views/allinvoices.php">Tüm Faturalar</a></li>
        <?php if ($_SESSION["usertype"] == "superuser"): ?>
            <li><a href="manageusers.php">Manage Users</a></li>
        <?php endif; ?>
        <li class="active"><a href="upload.php">Fatura Yükle</a></li> 
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="profile.php"><span class="glyphicon glyphicon-log-out"></span>Profile (<?php echo $_SESSION["username"]; ?>)</a></li>
      </ul>
    </div>
  </div>
</nav>


<body>
<div class="wrapper">
    <h2>XML Dosya Yükleme</h2>
    <form action="allinvoices-test.php" method="post" enctype="multipart/form-data">
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
}
</script>
</body>
</html>