<?php
/**
 * User creation page
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <github@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
// Include config file
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once SESSION_HELPER;

protectPage(['superuser']);
 
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
          $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        
          $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
         
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_email);
            
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            if (mysqli_stmt_execute($stmt)) {
                  header("location: manageusers.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($link);
} else {
    $email = "";
}
?>
 
<!DOCTYPE html>
<html lang="en" style="background-image: url('/public/images/background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Create User</title>
    <link rel="icon" type="image/x-icon" href="/public/icons/favicon.ico">

    <link rel="stylesheet" href="/public/css/styles.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Create User</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div> 
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>                
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="manageusers.php">Cancel </a>
            </div>
        </form>
    </div>    
</body>
</html>
