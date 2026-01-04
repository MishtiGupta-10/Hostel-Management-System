<?php
include("connect.php");

if (!isset($_POST['student_id'])) {
    echo "error";
    exit;
}

$student_id = mysqli_real_escape_string($conn, $_POST['student_id']);

$query = "DELETE FROM Student WHERE Student_Id = '$student_id'";

if (mysqli_query($conn, $query)) {
    echo "success";
} else {
    echo "error";
}
?>
