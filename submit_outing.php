<?php
session_start();
include("connect.php");

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    echo "error";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id = $_SESSION["user_id"];

    // Fetch student name
    $q = "SELECT CONCAT(Fname, ' ', Lname) AS Name 
          FROM Student 
          WHERE Student_Id = '$student_id'";

    $res = mysqli_query($conn, $q);
    $stu = mysqli_fetch_assoc($res);
    $name = $stu["Name"];

    // Input values
    $purpose = mysqli_real_escape_string($conn, $_POST["purpose"]);
    $exit_time = mysqli_real_escape_string($conn, $_POST["from_date"]);
    $entry_time = mysqli_real_escape_string($conn, $_POST["to_date"]);

    // Insert into Home_Entry
    $query = "INSERT INTO Home_Entry(Student_Id, Name, Exit_Time, Entry_Time, status, purpose)
              VALUES('$student_id', '$name', '$exit_time', '$entry_time', 0, '$purpose')";

    if (mysqli_query($conn, $query)) {
        header("Location: student_dashboard.php?message=outing_requested");
        exit();
    } else {
        echo "error";
    }
}
?>
