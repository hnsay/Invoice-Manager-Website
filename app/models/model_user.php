<?php
/**
 * User SQL functions.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <say@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */

function getAllUsernames($link) 
{
    $sql = "SELECT username FROM users ORDER BY username ASC";
    $result = mysqli_query($link, $sql);
    $array = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $array[] = $row;
    }
    return $array;
}

function getAllUsers($link) 
{
    $sql = "SELECT * FROM users";
    $result = mysqli_query($link, $sql);
    $array = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $array[] = $row;
    }
    return $array;
}

function getActiveUsers($link)
{
    $sql = "SELECT username, email, usertype, mailgroup FROM users WHERE NOT usertype='mailgroup'";
    $result = mysqli_query($link, $sql);
    $array = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $array[] = $row;
    }
    return $array;
}

function getMailGroup($link, $username) {
    $stmt = mysqli_prepare($link, "SELECT mailgroup FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($result, MYSQLI_ASSOC)['mailgroup'];
}

function getMailGroups($link)
{
    $sql = "SELECT username, email FROM users WHERE usertype='mailgroup'";
    $result = mysqli_query($link, $sql);
    $array = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $array[] = $row;
    }
    return $array;
}


function getUserType($link, $username) 
{
    $stmt = mysqli_prepare($link, "SELECT usertype FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_array($result, MYSQLI_ASSOC)['usertype'];
}

function checkUserExists($link, $username) 
{
    $sql = "SELECT id FROM users WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = trim($username);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_close($stmt);
                return "This groupname is already taken.";
            } else {
                mysqli_stmt_close($stmt);
                return null;
            }
        } else {
            mysqli_stmt_close($stmt);
            return "Something went wrong. Please try again later.";
        }
    } else {
        return "Something went wrong. Please try again later.";
    }
}

function createMailGroup($link, $groupname, $email) 
{
    $sql = "INSERT INTO users (username, password, email, usertype) VALUES (?, ?, ?, 'mailgroup')";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_email);
        $param_username = $groupname;
        $param_email = $email;
        $randomString = bin2hex(random_bytes(32));
        $param_password = password_hash($randomString, PASSWORD_DEFAULT); //Unknown pass

        if (mysqli_stmt_execute($stmt)) {
            return "Mail group created successfully.";
        } else {
            mysqli_stmt_close($stmt);
            return "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    } else {
        return "Something went wrong. Please try again later.";
    }
}

function deleteUser($link, $username)
{
    $sql = "DELETE from users where username=?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $param_name);
    $param_name= $username;
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    }
    else return false;
}
?>