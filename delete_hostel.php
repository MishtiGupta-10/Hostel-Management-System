<?php
include("connect.php");
$id = $_POST['hostelId'];

$query = "DELETE FROM Hostel WHERE Hostel_Id = $id";

if(mysqli_query($conn, $query)){
    echo "Hostel deleted successfully!";
} else {
    echo "Error deleting hostel: " . mysqli_error($conn);
}
?>
