<?php
session_start();

if (!isset($_SESSION['userid'])) {
    echo "<script>alert('Login to continue.'); window.location.href='login.php';</script>";
    exit;
}

include 'connection.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id']; 
    $rate= $_GET['rate']; 
    $userid = $_SESSION['userid'];

    $check_query = "SELECT * FROM cart WHERE product_id = '$product_id' AND userid = '$userid' AND status = '1'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Product already in cart.'); window.location.href='products.php';</script>";
    } else {
        $sql = "INSERT INTO cart (product_id, userid,quantity,rate,total,status) VALUES ('$product_id', '$userid',1,'$rate','$rate', 0)";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Product added to cart.'); window.location.href='products.php';</script>";
        } else {
            echo "<script>alert('Error adding product.'); window.location.href='products.php';</script>";
        }
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href='products.php';</script>";
}
?>
