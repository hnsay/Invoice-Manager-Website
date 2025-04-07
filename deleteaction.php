<?php
/**
 * ?????.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <say@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
$text ="";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
      header("Location:login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
      exit;
}

if ($_SESSION["usertype"] != "superuser") {
      header("location: 404.php");
      exit;
}

require_once "config.php";
require_once "error_log.php";
?>

<!DOCTYPE html>
<html lang="en" style="background-image: url('background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Delete User</title>
    <link rel="stylesheet" href="styles.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        select{ text-align: left;}
        .wrapper{ padding: 20px; }
    
    </style>
</head>


<body><div class="wrapper">
<h2>Delete User</h2><br>
<p><?php echo $text ?></p>
<form class="form-group" id="myform"><br>
  <a class="btn btn-info" href="<?php echo isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : 'index.php'; ?>">Return</a>
</form>
</div></body>

