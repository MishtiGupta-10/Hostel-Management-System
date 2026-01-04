<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST["student_id"])) {
        echo "error";
        exit();
    }

    $student_id = $_POST["student_id"];
    $query = "UPDATE Home_Entry 
              SET Exit_Time = NOW(), status = 1
              WHERE Student_Id = '$student_id'
              AND Entry_Time IS NULL";

    if (mysqli_query($conn, $query)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
