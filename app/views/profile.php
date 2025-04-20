<?php
/**
 * Profile management page.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <github@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once SESSION_HELPER;

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en" style="background-image: url('/public/images/background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Profil Yönetimi</title>
    <link rel="icon" type="image/x-icon" href="/public/icons/favicon.ico">

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