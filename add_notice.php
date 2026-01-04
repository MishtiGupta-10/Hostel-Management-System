<?php
include("connect.php");
session_start();

$warden_id = $_SESSION['user_id'];

// Fetch hostel of this warden
$hostelQuery = "SELECT Hostel_Id FROM Hostel WHERE Warden_Id='$warden_id' LIMIT 1";
$hostelResult = mysqli_query($conn, $hostelQuery);
$wardenData = mysqli_fetch_assoc($hostelResult);

$hostel_id = $wardenData['Hostel_Id'];

$title = mysqli_real_escape_string($conn, $_POST['title']);
$description = mysqli_real_escape_string($conn, $_POST['description']);

$query = "INSERT INTO Notice (hostel_id, warden_id, title, description, date)
          VALUES ('$hostel_id', '$warden_id', '$title', '$description', NOW())";

if(mysqli_query($conn, $query)){
    echo "success";
} else {
    echo "error";
}
?>
