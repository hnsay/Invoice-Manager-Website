<?php
/**
 * Delete users.
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

protectPage(['superuser']);

$sql = "SELECT username FROM users";
$result = mysqli_query($link, $sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['post'] == "admin") {
    } else if (Delete_user($link, $_POST['post']) ) {
        header("Location: manageusers.php?status=deleted&user=" . urlencode($_POST['post']));
        exit;
    }
    else echo "Something went wrong please try again later.";
}

function Delete_user($link, $username)
{
    $sql = "DELETE from users where username=?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $first);
    $first= $username;
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    }
    else return false;
}


?>

<!DOCTYPE html>
<html lang="en" style="background-image: url('/public/images/background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Delete User</title>
    <link rel="stylesheet" href="/public/css/styles.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        select{ text-align: left;}
        .wrapper{ width: 250px; padding: 20px; }
    
    </style>
</head>


<body><div class="wrapper">
<h2>Delete User</h2>
<form class="form-group" id="myform" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"><br>
    <p>Please select user to delete</p>
      <select class="form-control" name="post">
        <?php while ($row=mysqli_fetch_array($result)) {
            ?> <option ><?php echo $row['username'] ?></option><?php
        }?>
      </select><span class="help-block"></span><br>
      <button type="submit" class="btn btn-danger" onclick="return confirm('You are about to delete the selected user, are you sure?')">Delete</button>
      <a class="btn btn-link" href="manageusers.php">Cancel</a>
</form>
</div></body>
<html>

