<?php
// cancel_order.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connection.php";

if (!isset($_SESSION["userid"]) || !isset($_GET["orderno"])) {
    header("Location: myorders.php");
    exit;
}

$uid = $_SESSION["userid"];
$orderno = mysqli_real_escape_string($conn, $_GET["orderno"]);

// Update the order status to 5 (Cancelled)
$qry = "UPDATE orders SET status = 5 WHERE orderno = '$orderno' AND userid = '$uid' AND status = 1";

if (mysqli_query($conn, $qry)) {
    // Success: Redirect back to the orders page
    header("Location: myorders.php?message=Order #$orderno cancelled successfully.");
    exit;
} else {
    // Error: Redirect with an error message
    header("Location: myorders.php?error=Failed to cancel order #$orderno. Status may be too advanced.");
    exit;
}
?>