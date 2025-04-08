<?php
// Start session and auth check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ||
    ($_SESSION["usertype"] !== "superuser" && $_SESSION["usertype"] !== "admin")) {
    header("Location: login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Include DB config
require_once "/config/config.php";
require_once "error_log.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $inputData = json_decode(file_get_contents("php://input"), true);
    $result = [
        "uploaded" => 0,
        "failed" => 0,
        "errors" => []
    ];

    if (is_array($inputData)) {
        foreach ($inputData as $row) {
            $supplier = $row[0];
            $no = $row[1];
            $date = $row[2];
            $amount = $row[3];
            $currency = $row[4];
            $description = $row[5];
            $lines = $row[6];

            try {
                Upload_invoice($link, $supplier, $no, $date, $amount, $currency, $description);
                Clear_invoicelines($link, $no);

                foreach ($lines as $line) {
                    Upload_invoiceline($link, $line[0], $line[1], $line[2], $line[3], $line[4]);
                }

                $result["uploaded"]++;
            } catch (Exception $e) {
                $result["failed"]++;
                $result["errors"][] = [
                    "invoice" => $no,
                    "error" => $e->getMessage()
                ];
            }
        }
    }

    echo json_encode($result);
    exit;
}

// Upload functions with error handling
function Upload_invoice($link, $supplier, $no, $date, $amount, $currency, $description)
{
    $stmt = mysqli_prepare($link, "INSERT INTO invoices(supplier, no, state, date, amount, currency, description) VALUES (?, ?, 'Bekliyor', ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed for invoice: " . mysqli_error($link));
    }

    mysqli_stmt_bind_param($stmt, "ssssss", $supplier, $no, $date, $amount, $currency, $description);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed for invoice [$no]: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
}

function Clear_invoicelines($link, $no)
{
    $stmt = mysqli_prepare($link, "DELETE FROM invoicelines WHERE no = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed for line clear: " . mysqli_error($link));
    }

    mysqli_stmt_bind_param($stmt, "s", $no);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed for clearing lines [$no]: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
}

function Upload_invoiceline($link, $no, $product, $quantity, $unitprice, $price)
{
    $stmt = mysqli_prepare($link, "INSERT INTO invoicelines(no, product, quantity, unitprice, price) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed for line: " . mysqli_error($link));
    }

    mysqli_stmt_bind_param($stmt, "sssss", $no, $product, $quantity, $unitprice, $price);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed for line [$no]: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
}
?>
