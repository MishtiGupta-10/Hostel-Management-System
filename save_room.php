<?php
include("connect.php");

$room_no = $_POST['room_no'];
$cap = $_POST['cap'];
$occ = $_POST['occ'];

if ($occ > $cap) {
    die("Occupied cannot be greater than capacity");
}

$query = "UPDATE Room 
          SET Capacity = '$cap', Occupied = '$occ' 
          WHERE Room_No = '$room_no'";

if (mysqli_query($conn, $query)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($conn);
}
?>
