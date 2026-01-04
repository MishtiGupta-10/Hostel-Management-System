<?php
include("connect.php");

if (isset($_POST['resolve'])) {
    $id = $_POST['id'];
    $query = "UPDATE complaint 
              SET Status = 1, Resolved_On = NOW()
              WHERE Complaint_Id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}


if (isset($_POST['resolve'])) {
    $id = $_POST['id'];
    $query = "UPDATE complaint SET Status = 1, Resolved_On = NOW() WHERE Complaint_Id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: complaint_management.php");
        exit();
    } else {
        echo "Error updating status: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}


mysqli_close($conn);
header("Location: complaint_management.php");
exit;
?>
