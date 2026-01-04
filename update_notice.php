<?php
include("connect.php");
session_start();

$warden_id = $_SESSION['user_id'];

$id = $_POST['id'];
$title = mysqli_real_escape_string($conn, $_POST['title']);
$description = mysqli_real_escape_string($conn, $_POST['description']);

$query = "UPDATE Notice
          SET title='$title', description='$description'
          WHERE notice_id='$id' AND warden_id='$warden_id'";

if(mysqli_query($conn, $query)){
    echo "success";
} else {
    echo "error";
}
?>
