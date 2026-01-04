    <?php
    include("connect.php");

    //ADMIN PASSWORD HASHING
    $AdminPassword = "admin123";
    $adminHash = password_hash($AdminPassword, PASSWORD_DEFAULT);

    $updateAdmin = "UPDATE admin SET Password = '$adminHash' where username = 'admin'";
    if(mysqli_query($conn, $updateAdmin))
    {
        echo "Admin passwords updated successfully!<br>";
    }
    else 
    {
        echo "Error updating admin passwords: " . mysqli_error($conn) . "<br>";
    }

    //WARDEN PASSWORD HASHING
    $WardenPassword = "warden123";
    $WardenHash = password_hash($WardenPassword, PASSWORD_DEFAULT);

    $updateWarden = "UPDATE Warden SET Password = '$WardenHash'";
    if (mysqli_query($conn, $updateWarden)) {
        echo "Warden table passwords updated successfully!<br>";
    } else {
        echo "Error updating warden table passwords: " . mysqli_error($conn) . "<br>";
    }



    //STUDENT PASSWORD HASHING
    $StudentPassword = "hostel123";
    $studentHash = password_hash($StudentPassword, PASSWORD_DEFAULT);

    $updateStudent = "UPDATE student SET Password = '$studentHash'";
    if (mysqli_query($conn, $updateStudent)) {
        echo "Student passwords updated successfully!<br>";
    } else {
        echo "Error updating student passwords: " . mysqli_error($conn) . "<br>";
    }
    ?>