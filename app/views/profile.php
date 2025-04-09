<?php
/**
 * Profile management page.
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

require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php"; 

//check if the user is admin or superuser

$sql = "SELECT * FROM users where username=". "'" . $_SESSION["username"] . "'";
$result = mysqli_query($link, $sql);
$user = mysqli_fetch_array($result);

if (!isset($_SESSION["usertype"])) {
    if ($user['superuser'] == "1") {
        $_SESSION["usertype"] = "superuser"; 
    } else if ($user['admin'] == "1") {
        $_SESSION["usertype"] = "admin";
    } else {
        $_SESSION["usertype"] = "user";
    }
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
}


$sql = "SELECT * FROM invoices where state='Pending' AND assignee = " . "'" . $_SESSION["username"] . "'";
$result = mysqli_query($link, $sql);

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en" style="background-image: url('/public/images/background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Profil Yönetimi</title>
    <link rel="stylesheet" href="/public/css/styles.css">
    <style type="text/css">
        body{ font: 12px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
</style>
</head>

<?php require 'navbar.php'; ?>

<body>

<body>
    <div class="wrapper">
        <a class="btn btn-primary" href="reset-password.php" style="margin-right:20px;">Şifre Değiştir</a>
        <a class="btn btn-danger" href="/helpers/logout.php">Çıkış Yap</a>
        <label style="margin-top:20px;font-size: 20px;">Kullanıcı Türü: {<?php echo $_SESSION["usertype"];?>}</label>
    </div>  

</body>
</html>