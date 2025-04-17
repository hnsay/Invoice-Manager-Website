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

require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
require_once SESSION_HELPER;
require_once MODEL_USER;
protectPage(['superuser']);
 
// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }
    // Validate username
    if (empty(trim($_POST["groupname"]))) {
        $groupname_err = "Please enter a group name.";
    } else {
        $groupname_err = checkUserExists($link, $_POST["groupname"]);
        if (!$groupname_err) {
            $groupname = trim($_POST["groupname"]);
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email = null;
    } else {
        $email = trim($_POST["email"]);
    }
    
    
    if (empty($groupname_err)) {
        $response = createMailGroup($link, $groupname, $email);
    }
}
?>
 
<!DOCTYPE html>
<html lang="en" style="background-image: url('/public/images/background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Create Mail Group</title>
    <link rel="stylesheet" href="/public/css/styles.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php echo (isset($response) && !empty($response)) ? '<h2 style="color: green;">' . $response . '</h2>' : '<h2>Create Mail Group</h2>'; ?>
        <p>Please fill this form to create a mail group.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (isset($groupname_err) && !empty($groupname_err)) ? 'has-error' : null; ?>">
                <label>Groupname</label>
                <input type="text" name="groupname" class="form-control" value="<?php echo isset($groupname) ? $groupname : null; ?>">
                <span class="help-block"><?php echo (isset($groupname_err)) ? $groupname_err : null; ?></span>
            </div> 
            <div class="form-group <?php echo (isset($email_err) && !empty($email_err)) ? 'has-error' : null; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo isset($email) ? $email : null; ?>">
            </div>                
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="manageusers.php">Cancel</a>
            </div>
        </form>
    </div>    
</body>
</html>
