<?php
    include "../connection.php";
    $ono = $_GET['orderno'];

    $q = "update orders set status = 3 where orderno = '$ono'";
    mysqli_query($conn,$q);

    echo "<script>alert('Order Shipped');window.location='index.php';</script>";
?>