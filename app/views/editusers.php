<?php
/**
 * Edit Users.
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
 
// Define variables and initialize with empty values
$email = "";
$email_err = "";

$sql = "SELECT username, email FROM users WHERE NOT usertype='mailgroup'";
$result1 = mysqli_query($link, $sql);

$users = [];
while ($row = mysqli_fetch_assoc($result1)) {
    $users[] = $row;
}

$sql = "SELECT username, email FROM users WHERE usertype='mailgroup'";
$result2 = mysqli_query($link, $sql);

$groups = [];
while ($row = mysqli_fetch_assoc($result2)) {
    $groups[] = $row;
}



// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($_POST['usertype'] == "superuser" && !( $_SESSION["username"] == "halil"  || $_SESSION["username"] == "hasan" || $_POST['username'] == $_SESSION['username'])) {
        $email_err = "This user is protected, you do not have the required permissions to make changes.";
    } else {
        if ($_POST['username'] == "halil" || $_POST['username'] == "hasan") {
            $email_err = "This user is protected, you do not have the required permissions to make changes.";
        } else {
            // Validate email
            if (empty(trim($_POST["email"])) ) {
                $email_err = "Please enter an email address";
            } else {
                $email = trim($_POST["email"]);
            }
    
            // Check input errors before updating the database
            if (empty($email_err) ) {
                // Prepare an update statement
                  $sql = "UPDATE users SET email = ?, usertype = ?, mailgroup = ? WHERE username = ?";
    
                if ($stmt = mysqli_prepare($link, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "ssss", $param_email, $param_usertype, $param_mailgroup, $param_username);
    
                    // Set parameters
                    $param_email = $_POST["email"];
                    $param_usertype = $_POST["usertype"];
                    ($_POST['mailgroup'] == "null") ? ($param_mailgroup = null) : ($param_mailgroup = $_POST["mailgroup"]);
                    $param_username = $_POST["username"];
    
                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {
                        // Password updated successfully. Destroy the session, and redirect to login page
                          header("location: editusers.php");
                        exit();
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
    
                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            } else {
                // Close connection
                mysqli_close($link);
            }
        }
    }

}
?>
 
<!DOCTYPE html>
<html lang="en" style="background-image: url('/public/images/background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Edit Users and Groups</title>
    <link rel="stylesheet" href="/public/css/styles.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>

<div class="wrapper">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
        <h2 style="margin-bottom:20px;">Edit User</h2>
            <div class="form-group">
                <label>Select User to Change</label>
                <select class="form-control" name="username" onchange="getEmail(this);">
                <?php foreach ($users as $row) {
                    ?><option ><?php echo $row['username'] ?></option>
                <?php }?>
                </select>
            </div>

            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="email" name="email" class="form-control" id="email">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>

            <div class="form-group">
            <label style="margin-top:10px;">User Type</label>
            <select class="form-control" name="usertype">
            <option>user</option>
            <option>admin</option>
            <?php // if ($_SESSION["username"] == "halil" || $_SESSION["username"] == "hasan") {
                //echo "<option>superuser</option>";
            //}
            ?>
            <option>superuser</option>
            </select>
            </div>

            <div class="form-group">
            <label>Mailgroup</label>
            <select class="form-control" name="mailgroup">
            <option>null</option>
            <?php foreach ($groups as $row) {
                ?><option><?php echo $row['username'] ?></option>
            <?php }?>
            </select>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="manageusers.php">Cancel</a>
            </div>

        </form>
    </div>

    <script>

        // Access the array elements 
       var users = <?php echo json_encode($users); ?>;

       function getEmail(selection){
        var email = users.filter(obj => {return obj.username === selection.value})[0].email;
        document.getElementById("email").value = email;
       }
    </script>
</body>
</html>
