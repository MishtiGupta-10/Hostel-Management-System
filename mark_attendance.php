<?php
session_start();
include("connect.php");

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    echo "error";
    exit();
}

$student_id = $_SESSION["user_id"];

$q = "SELECT 
        CONCAT(S.Fname, ' ', S.Lname) AS Name,
        S.Room_Id,
        R.Room_No
      FROM Student S
      INNER JOIN Room R ON S.Room_Id = R.Room_Id
      WHERE S.Student_Id = '$student_id' LIMIT 1";

$res = mysqli_query($conn, $q);

if (!$res || mysqli_num_rows($res) == 0) {
    echo "error";
    exit();
}

$stu = mysqli_fetch_assoc($res);
$name = $stu["Name"];
$room_id = $stu["Room_Id"];
$room_no = $stu["Room_No"];

$checkQuery = "
    SELECT COUNT(*) AS count 
    FROM Attendance 
    WHERE Student_Id = '$student_id' 
    AND DATE(time) = CURDATE()
";

$checkRes = mysqli_query($conn, $checkQuery);
$checkData = mysqli_fetch_assoc($checkRes);

if ($checkData['count'] > 0) {
    echo "already_marked";
    exit();
}


$insert = "INSERT INTO Attendance (Student_Id, Name, Room_Id, Room_No, time)
           VALUES('$student_id', '$name', '$room_id', '$room_no', NOW())";

if (mysqli_query($conn, $insert)) {
    echo "success";
} else {
    echo "error";
}
?>
