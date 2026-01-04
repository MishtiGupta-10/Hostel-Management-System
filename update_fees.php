<?php

include ("connect.php");

if(isset($_POST['Mark_Paid']))
{
    $id = $_POST['id'];
    $query = "UPDATE fee
              SET Status = 1
              WHERE Fee_Id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

if(isset($_POST['Mark_Pending']))
{
    $id = $_POST['id'];
    $query = "UPDATE fee
              SET Status = 0
              WHERE Fee_Id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

mysqli_close($conn);
header("Location: manage_fees.php");
exit;

?>