<?php
date_default_timezone_set('Asia/Kolkata');

session_start();
include ("connect.php");

if(!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$student_id = $_SESSION['user_id'];
$type = $_POST['type'];
$desc = $_POST['desc'];

if(empty($type) || empty($desc)) {
    die("Please fill all fields");
}

$applied_on = date("Y-m-d H:i:s");


$query = "INSERT INTO Complaint (Student_Id, Type, description, Status, Applied_On) 
          VALUES ('$student_id', '$type', '$desc', 0, '$applied_on')";

if (mysqli_query($conn, $query)) {
    header("Location: student_dashboard.php?complaint=success");
    exit();
} else {
    echo "Error: " . mysqli_error($conn);
}
?>