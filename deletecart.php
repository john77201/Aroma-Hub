<?php
    include "connection.php";
    $id=$_GET['id'];

    $qry="delete from cart where cart_id='$id'";
    mysqli_query($conn,$qry);
    echo "<script>window.location='shoppingcart.php';</script>";
?>