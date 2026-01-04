<?php
session_start();
include("connect.php");

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    echo "error";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id = $_SESSION["user_id"];
    $type = mysqli_real_escape_string($conn, $_POST["type"]);
    $desc = mysqli_real_escape_string($conn, $_POST["desc"]);

    // Fetch Hostel ID of logged-in student
    $hostel_q = "SELECT Hostel_Id FROM Student WHERE Student_Id = '$student_id' LIMIT 1";
    $hostel_res = mysqli_query($conn, $hostel_q);
    $hostel_data = mysqli_fetch_assoc($hostel_res);
    $hostel_id = $hostel_data["Hostel_Id"];

    // Insert Complaint
    $query = "INSERT INTO Complaint(student_id, Hostel_Id, Type, description, Status, Applied_On) 
              VALUES('$student_id', '$hostel_id', '$type', '$desc', 0, NOW())";

    if (mysqli_query($conn, $query)) {
        header("Location: student_dashboard.php?message=complaint_saved");
        exit();
    } else {
        echo "error";
    }
}
?>
