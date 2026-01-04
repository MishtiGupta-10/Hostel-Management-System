<?php
include("connect.php");

$id       = mysqli_real_escape_string($conn, $_POST['id']);
$name     = mysqli_real_escape_string($conn, $_POST['name']);
$roomNo   = mysqli_real_escape_string($conn, $_POST['room']);
$hostelName = mysqli_real_escape_string($conn, $_POST['hostel']);
$contact  = mysqli_real_escape_string($conn, $_POST['contact']);


$nameParts = explode(" ", $name, 2);
$fname = $nameParts[0];
$lname = isset($nameParts[1]) ? $nameParts[1] : "";


$roomQuery = "SELECT Room_Id FROM Room WHERE Room_No = '$roomNo' LIMIT 1";
$roomRes = mysqli_query($conn, $roomQuery);
$roomData = mysqli_fetch_assoc($roomRes);

if (!$roomData) {
    echo "error";
    exit;
}

$room_id = $roomData['Room_Id'];


$hostelQuery = "SELECT Hostel_Id FROM Hostel WHERE Name = '$hostelName' LIMIT 1";
$hostelRes = mysqli_query($conn, $hostelQuery);
$hostelData = mysqli_fetch_assoc($hostelRes);

if (!$hostelData) {
    echo "error";
    exit;
}

$hostel_id = $hostelData['Hostel_Id'];

$query = "UPDATE Student 
          SET Fname='$fname', 
              Lname='$lname', 
              Room_Id='$room_id', 
              Hostel_Id='$hostel_id',
              contact_info='$contact'
          WHERE Student_Id='$id'";

if (mysqli_query($conn, $query)) {
    echo "success";
} else {
    echo "error";
}
?>
