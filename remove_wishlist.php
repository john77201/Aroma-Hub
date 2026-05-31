<?php
include 'connection.php';
session_start();

// Check if user is logged in
if(!isset($_SESSION['userid'])){
    echo "<script>window.location='login.php';</script>";
    exit;
}

// Validate wishlist id
if(isset($_GET['id'])){
    $wid = intval($_GET['id']);
    $userid = $_SESSION['userid'];

    // Delete only the wishlist item that belongs to this user
    $qry = "DELETE FROM wishlist WHERE id='$wid' AND user_id='$userid'";
    mysqli_query($conn, $qry);
}

// Redirect back to wishlist
echo "<script>window.location='wishlist.php';</script>";
exit;
?>
