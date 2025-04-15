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
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
require_once SESSION_HELPER;

//check if the user is admin or superuser
if (!isset($_SESSION["usertype"])) {
    $sql = "SELECT usertype FROM users WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $_SESSION["username"];
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $usertype);
            if (mysqli_stmt_fetch($stmt)) {
                $_SESSION["usertype"] = $usertype;
            }
            else $_SESSION["usertype"] = "user";
        }
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