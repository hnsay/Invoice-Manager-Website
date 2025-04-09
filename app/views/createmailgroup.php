<?php
/**
 * Create mail group.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <say@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
// Include config file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
      header("Location:app/views/login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
      exit;
}

if ($_SESSION["usertype"] != "superuser") {
      header("location: app/views/404.php");
      exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
 
// Define variables and initialize with empty values
$groupname = "";
$groupname_err = "";
$email = "";

 
// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }
    // Validate username
    if (empty(trim($_POST["groupname"]))) {
        $groupname_err = "Please enter a group name.";
    } else {
        // Prepare a select statement
          $sql = "SELECT id FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["groupname"]);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $groupname_err = "This groupname is already taken.";
                } else {
                    $groupname = trim($_POST["groupname"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate email

    if (empty(trim($_POST["email"]))) {
        $email = null;
    } else {
        $email = trim($_POST["email"]);
    }
    
    
    // Check input errors before inserting in database
    if (empty($groupname_err)) {
        
        // Prepare an insert statement
          $sql = "INSERT INTO users (username, password, email, usertype) VALUES (?, ?, ?, 'mailgroup')";
         
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_email);
            
            // Set parameters
            $param_username = $groupname;
            $param_email = $email;
            $param_password = password_hash("notSuPosSedTobEKnoWn", PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                  header("location: manageusers.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en" style="background-image: url('background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Create Mail Group</title>
    <link rel="stylesheet" href="styles.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Create Mail Group</h2>
        <p>Please fill this form to create a mail group.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($groupname_err)) ? 'has-error' : ''; ?>">
                <label>Groupname</label>
                <input type="text" name="groupname" class="form-control" value="<?php echo $groupname; ?>">
                <span class="help-block"><?php echo $groupname_err; ?></span>
            </div> 
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
            </div>                
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="manageusers.php">Cancel</a>
            </div>
        </form>
    </div>    
</body>
</html>
