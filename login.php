<?php
/**
 * Login Page.
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
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
      header("location: welcome.php");
    exit();
}

$redirect = null;
if (isset($_POST['location']) && $_POST['location'] != '') {
    $redirect = $_POST['location'];
}
 
// Include config file
require_once "config.php";
require_once "error_log.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
          $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            if (session_status() == PHP_SESSION_NONE) {
                                session_start();
                            }
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;


                              $sql = "SELECT * FROM users where username=". "'" . $_SESSION["username"] . "'";
                            $result = mysqli_query($link, $sql);
                            $user = mysqli_fetch_array($result);

                            if (!isset($_SESSION["usertype"])) {
                                if ($user['usertype'] == "superuser") {
                                    $_SESSION["usertype"] = "superuser"; 
                                } else if ($user['usertype'] == "admin") {
                                    $_SESSION["usertype"] = "admin";
                                } else {
                                    $_SESSION["usertype"] = "user";
                                }
                            }
                            // Redirect user to welcome page
                            if ($redirect) {
                                  header("Location:". $redirect);
                            } else {
                                  header("location: welcome.php");
                            }

                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
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
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href = "mailto: github@hnsay.com.tr">Send Email</a>.</p>
            <?php   echo '<input type="hidden" name="location" value="';
            if (isset($_GET['location'])) {
                echo htmlspecialchars($_GET['location']);
            }
             echo '" />'; ?>
        </form>
    </div>    
</body>
</html>
