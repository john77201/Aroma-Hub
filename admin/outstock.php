<?php
    include "../connection.php";
    $id = $_GET['id'];

    $qry = "update product set stock_status = 'Out Of Stock' where product_id = '$id'";
    mysqli_query($conn,$qry);

    echo "<script>alert('Marked as Out Of Stock');window.location='view_products.php';</script>";
?>