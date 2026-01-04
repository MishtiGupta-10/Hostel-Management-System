<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('HTTP/1.1 403 Forbidden');
    echo "forbidden";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['entry_id'])) {
    echo "invalid";
    exit();
}

$entry_id = mysqli_real_escape_string($conn, $_POST['entry_id']);
$student_id = $_SESSION['user_id'];

$check = "SELECT Entry_Id, Exit_Time, Entry_Time FROM Home_Entry WHERE Entry_Id = '$entry_id' AND Student_Id = '$student_id' LIMIT 1";
$res = mysqli_query($conn, $check);
$row = mysqli_fetch_assoc($res);

if (!$row) {
    echo "not_found";
    exit();
}

if (!empty($row['Exit_Time']) || !empty($row['Entry_Time'])) {
    echo "cannot_cancel";
    exit();
}
$del = "DELETE FROM Home_Entry WHERE Entry_Id = '$entry_id' AND Student_Id = '$student_id' LIMIT 1";
if (mysqli_query($conn, $del)) {
    header("Location: student_dashboard.php");
    exit();
} else {
    echo "sql_error: " . mysqli_error($conn);
    exit();
}
?>
