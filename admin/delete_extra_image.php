<?php
include "../connection.php";

if(isset($_GET['id'])){
    $id = $_GET['id'];

    $qry = mysqli_query($conn, "SELECT image_name FROM product_images WHERE image_id='$id'");
    $row = mysqli_fetch_assoc($qry);
    if($row){
        $image_path = "../uploads/".$row['image_name'];
        if(file_exists($image_path)){
            unlink($image_path);
        }
        mysqli_query($conn, "DELETE FROM product_images WHERE image_id='$id'");
    }
}
?>
