<?php
session_start();
include("connect.php");

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    echo "error";
    exit();
}

$student_id = $_SESSION["user_id"];

// Fetch student name
$q = "SELECT CONCAT(Fname, ' ', Lname) AS Name 
      FROM Student 
      WHERE Student_Id = '$student_id' LIMIT 1";
$res = mysqli_query($conn, $q);
$stu = mysqli_fetch_assoc($res);

$name = $stu["Name"];

// Insert a pending outing request
$insert = "INSERT INTO Home_Entry (Student_Id, Name, Exit_Time, Entry_Time, status)
           VALUES ('$student_id', '$name', NULL, NULL, 0)";

if (mysqli_query($conn, $insert)) {
    echo "success";
} else {
    echo "sql_error: " . mysqli_error($conn);
}
?>
