<?php
include("connect.php");

$id = $_POST['hostelId'];
$name = $_POST['name'];
$type = $_POST['type'];
$capacity = $_POST['capacity'];
$wardenId = $_POST['wardenId'];

$query = "UPDATE hostel SET Name = '$name', Type = '$type', Capacity = '$capacity', Warden_Id = '$wardenId' WHERE Hostel_Id = '$id'";

if(mysqli_query($conn, $query))
{
    echo "<script>alert('Hostel updated successfully');</script>";
}
else 
{
    echo "<script>alert('Error updating hostel:".mysqli_error($conn)."');</script>";
}

mysqli_close($conn);





?>