<?php
session_start();
include ("connect.php");

if(!isset($_SESSION["user_id"])) {
    die("Not logged in");
}

$field = $_POST["field"];
$value = trim($_POST["value"]);
$userId = $_SESSION["user_id"];

if(!in_array($field, ["contact", "email"])) {
    die("Invalid field");
}

$column = ($field === "contact") ? "contact_info" : "Email";

if($field === "email" & !filter_var($value, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

if($field === "contact" && !preg_match("/^[0-9]{10}$/", $value)) {
    die("Invalid contact number");
}

$query = "UPDATE Student SET $column = '$value' WHERE Student_Id = $userId";

if(mysqli_query($conn, $query)) {
    header("Location: student_profile.php?updated=1");
    exit();
} else {
    die("Update failed: " . mysqli_error($conn));
}
?>