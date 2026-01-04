<?php
include("connect.php");

$student_id = $_GET['id']; 

$query = "SELECT * FROM Home_Entry WHERE Student_Id = '$student_id'";
$res = mysqli_query($conn, $query);

if(!$res){
    echo "SQL Error: " . mysqli_error($conn);
    exit();
}

$row = mysqli_fetch_assoc($res);

echo "<pre>";
print_r($row);
echo "</pre>";
?>
