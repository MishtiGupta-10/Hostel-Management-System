<?php
include("connect.php");

if (isset($_POST['id']) && isset($_POST['remark'])) {
    $id = $_POST['id'];
    $remark = trim($_POST['remark']);

    $query = "UPDATE Complaint SET Remarks = ? WHERE Complaint_Id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if($stmt)
    {
        mysqli_stmt_bind_param($stmt, "si", $remark, $id);
    
        if(mysqli_stmt_execute($stmt))
        {
            echo "success";
            exit();
        }
        else{
            echo "Error executing query: " . mysqli_error($conn);
        }
    }
    else
    {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}
?>
