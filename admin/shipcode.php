<?php
    include "../connection.php";
    $ono = $_GET['orderno'];

    $q = "update orders set status = 4 where orderno = '$ono'";
    mysqli_query($conn,$q);

    echo "<script>alert('Order Delivered');window.location='index.php';</script>";
?>