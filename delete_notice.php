<?php
include("connect.php");
session_start();

$warden_id = $_SESSION['user_id'];
$id = $_POST['id'];

$query = "DELETE FROM Notice WHERE notice_id='$id' AND warden_id='$warden_id'";

if(mysqli_query($conn, $query)){
    echo "success";
} else {
    echo "error";
}
?>
