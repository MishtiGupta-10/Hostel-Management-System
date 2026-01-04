<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "warden") {
    header("Location: templates/login.html");
    exit();
}

include("connect.php");

$warden_id = $_SESSION["user_id"];

$hostelQuery = "SELECT Hostel_Id FROM Hostel WHERE Warden_Id = '$warden_id' LIMIT 1";
$res = mysqli_query($conn, $hostelQuery);
$hostelData = mysqli_fetch_assoc($res);
$hostel_id = $hostelData['Hostel_Id'];

$query = "SELECT 
            H.Entry_Id,
            S.Student_Id,
            CONCAT(S.Fname, ' ', S.Lname) AS Name,
            H.Exit_Time,
            H.Entry_Time
        FROM Home_Entry H
        INNER JOIN Student S ON H.Student_Id = S.Student_Id
        WHERE S.Hostel_Id = '$hostel_id'
        AND H.Exit_Time IS NOT NULL
        AND H.Entry_Time IS NOT NULL
        ORDER BY H.Entry_Id DESC";

$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Outing History</title>
    <link rel="stylesheet" href="css/outing.css">
</head>

<body>

<header class="header">
    <div class="logo">HMS</div>
    <nav class="nav-links">
        <a href="warden_dashboard.php">Home</a>
        <a href="outing.php">Manage Outings</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<div class="page-title">
    <h2>Completed Outings History</h2>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Exit Time</th>
                <th>Entry Time</th>
            </tr>
        </thead>

        <tbody>
            <?php 
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "
                    <tr>
                        <td>{$row['Name']}</td>
                        <td>{$row['Exit_Time']}</td>
                        <td>{$row['Entry_Time']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align:center;'>No past outings found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<footer>
    <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
</footer>

</body>
</html>
