<?php

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
      header("location: 403.php");
      exit;
}
 
// Include config file
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";


function Print_Invoice($supplier,$no,$date,$amount,$currency,$description,$taxex,$taxin,$vkn,$array){
    echo "
    <tr>
    <td>".$supplier."</td>
    <td>".$no."</td>
    <td>".$date."</td>
    <td>".$currency."</td>
    <td>".$array[0]."</td>
    <td>".$array[1]."</td>
    <td>".$array[2]."</td>
    <td>".$taxex."</td>
    <td>".$taxin."</td>
    <td>".$amount."</td>
    <td>".$vkn."</td>
    <td>"./*$description.*/"</td></tr>
    ";
}

function Print_InvoiceLine($no,$product,$quantity,$unitprice,$price){
        echo "<tr>
    <td></td>
    <td>".$no."</td>
    <td>".$product."</td>
    <td>".$quantity."</td>
    <td>".$unitprice."</td>
    <td>".$price."</td>
  </tr>
    ";
    
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


<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="welcome.php"><img src="/public/icons/favicon.ico"></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li><a href="welcome.php">Anasayfa</a></li>
        <li><a href="allinvoices.php">Tüm Faturalar</a></li>
        <?php if ($_SESSION["usertype"] == "superuser"): ?>
            <li><a href="manageusers.php">Manage Users</a></li>
        <?php endif; ?>  
        <li class="active"><a href="invoicelister.php">Fatura Yükle</a></li>    
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="profile.php"><span class="glyphicon glyphicon-log-out"></span>Profile (<?php echo $_SESSION["username"]; ?>)</a></li>
      </ul>
    </div>
  </div>
</nav>




<body>

<?php
$textArea = "";
echo "<br><br><br><br>
    <table class='table table-bordered'>
        <thead>
        <tr>
                  <th scope='col'>Supplier</th>
                  <th scope='col'>No</th>
                  <th scope='col'>Date</th>
                  <th scope='col'>Currency</th>
                <th scope='col'>Tax 1</th>
                <th scope='col'>Tax 2</th>
                <th scope='col'>Tax 3</th>
                  <th scope='col'>Tax Exclusive</th>
                  <th scope='col'>Tax Inclusive</th>
                  <th scope='col'>Amount</th>
                  <th scope='col'>VKN</th>
                  <th scope='col'>Description</th>
        </tr>
      </thead>";
if (isset($_POST["xmlFiles"])) {
    //print_r($_FILES);
        foreach( $_FILES[ 'xmlFiles' ][ 'tmp_name' ] as $location )
            {
                $xml = simplexml_load_file($location);
                $description = '';

                foreach ($xml->children('cbc', true)->Note as $note){
                    if (strpos($note, '#!#') === false ){
                    $description .= $note."\n";
                    }
                }

                $array = array();

                foreach ($xml->children('cac', true)->TaxTotal->children('cac', true)->TaxSubtotal as $subTotal){
                    $array[] = $subTotal->children('cbc', true)->TaxAmount;
                }

                if ($array[0] == null) {
                    $array[0] = "-";
                }

                if ($array[1] == null) {
                    $array[1] = "-";
                }

                if ($array[2] == null) {
                    $array[2] = "-";
                }
                
                if ($xml->children('cac', true)->AccountingSupplierParty->children('cac', true)->Party->children('cac', true)->PartyName->children('cbc', true)->Name == null) {
                    
                    $personName = $xml->children('cac', true)->AccountingSupplierParty->children('cac', true)->Party->children('cac', true)->Person->children('cbc', true)->FirstName.
                    " ".$xml->children('cac', true)->AccountingSupplierParty->children('cac', true)->Party->children('cac', true)->Person->children('cbc', true)->FamilyName.
                    " (Şahıs Şirketi)";
                    
                    Print_Invoice($personName,
                    $xml->children('cbc', true)->ID,
                    $xml->children('cbc', true)->IssueDate,
                    $xml->children('cac', true)->LegalMonetaryTotal->children('cbc', true)->PayableAmount,
                    $xml->children('cbc', true)->DocumentCurrencyCode,
                //$xml->xpath('//cbc:Note')[0]);
                    $description,
                    $xml->children('cac', true)->LegalMonetaryTotal->children('cbc', true)->TaxExclusiveAmount,
                    $xml->children('cac', true)->LegalMonetaryTotal->children('cbc', true)->TaxInclusiveAmount,
                    $xml->children('cac', true)->AccountingSupplierParty->children('cac', true)->Party->children('cac', true)->PartyIdentification->children('cbc', true)->ID,
                    $array);

                }
                else
                {
                    Print_Invoice($xml->children('cac', true)->AccountingSupplierParty->children('cac', true)->Party->children('cac', true)->PartyName->children('cbc', true)->Name,
                    $xml->children('cbc', true)->ID,
                    $xml->children('cbc', true)->IssueDate,
                    $xml->children('cac', true)->LegalMonetaryTotal->children('cbc', true)->PayableAmount,
                    $xml->children('cbc', true)->DocumentCurrencyCode,
                //$xml->xpath('//cbc:Note')[0]);
                    $description,
                    $xml->children('cac', true)->LegalMonetaryTotal->children('cbc', true)->TaxExclusiveAmount,
                    $xml->children('cac', true)->LegalMonetaryTotal->children('cbc', true)->TaxInclusiveAmount,
                    $xml->children('cac', true)->AccountingSupplierParty->children('cac', true)->Party->children('cac', true)->PartyIdentification->children('cbc', true)->ID,
                    $array);
                }

                //$textArea = $textArea.$xml->xpath('//cbc:ID')[0]." başarıyla yüklendi. \n";
            }
            $textArea = "Faturalar başarıyla yüklendi";
            
}
echo "</table>";

echo "<br><br><br><br>
    <table class='table table-bordered'><tr>
      <th>Supplier</th>
      <th>No</th>
      <th>Product</th>
      <th>Quantity</th>
      <th>Unit Price</th>
      <th>Price</th>
    </tr>";

if (isset($_POST["xmlFiles"])) {
    //print_r($_FILES);
        foreach( $_FILES[ 'xmlFiles' ][ 'tmp_name' ] as $location )
            {
                $xml = simplexml_load_file($location);
                $description = '';

                foreach ($xml->children('cac', true)->InvoiceLine as $invoiceLine)
                {
                    Print_InvoiceLine(
                    $xml->children('cbc', true)->ID,
                    $invoiceLine->children('cac', true)->Item->children('cbc', true)->Name,
                    $invoiceLine->children('cbc', true)->InvoicedQuantity,
                    $invoiceLine->children('cac', true)->Price->children('cbc', true)->PriceAmount,
                    $invoiceLine->children('cbc', true)->LineExtensionAmount);               
                }

                //$textArea = $textArea.$xml->xpath('//cbc:ID')[0]." başarıyla yüklendi. \n";
            }
            $textArea = "Faturalar başarıyla yüklendi";
            
}
echo "</table>";
$affectedRows = 0;
?>

<div class="wrapper">
    <h2>XML Dosya Yükleme</h2>
    <form action="invoicelister.php" method="post" enctype="multipart/form-data">
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