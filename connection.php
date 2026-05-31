<?php
$servername="localhost";
$username="root";
$password="";
$dbname="aromahub";
$conn=mysqli_connect($servername,$username,$password,$dbname);
if($conn)
{
   // echo("connection successfull");
}
else
{
    die("connection failed".mysqli_connect_error());
}
