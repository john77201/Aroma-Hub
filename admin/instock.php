<?php
include "../connection.php";

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $sql = "UPDATE product SET stock_status = 'In Stock' WHERE product_id = '$product_id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "<script>alert('Product marked as In Stock'); window.location='view_products.php';</script>";
    } else {
        echo "<script>alert('Failed to update'); window.location='view_products.php';</script>";
    }
}
?>
