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
            H.Entry_Time,
            H.status
        FROM Home_Entry H
        INNER JOIN Student S ON H.Student_Id = S.Student_Id
        WHERE S.Hostel_Id = '$hostel_id'
        AND (
                (H.Exit_Time IS NULL AND H.Entry_Time IS NULL)
             OR (H.Exit_Time IS NOT NULL AND H.Entry_Time IS NULL)
        )
        ORDER BY H.Entry_Id DESC";


$result = mysqli_query($conn, $query);
if (!$result) {
    die("SQL ERROR: " . mysqli_error($conn));
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outing Management</title>

    <link rel="stylesheet" href="css/outing.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

</head>

<body>

<!-- Header -->
<header class="header">
    <div class="logo">HMS</div>
    <nav class="nav-links">
        <a href="warden_dashboard.php">Home</a>
        <a href="student_records.php">Student Records</a>
        <a href="room_management.php">Rooms</a>
        <a href="complaint_management.php">Complaints</a>
        <a href="reports.php">Reports</a>
        <a href="warden_notice.php">Notices</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
</header>

<!-- Title -->
<div class="page-title">
    <h2>Outing Requests</h2>
</div>

<!-- Outing Table -->
<div class="table-container">
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Exit Time</th>
            <th>Entry Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php 
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                
                $statusText = ($row['status'] == 1) ? "Outside" : "Pending";
                $statusClass = ($row['status'] == 1) ? "pending" : "approved";

                echo "
                <tr data-id='{$row['Student_Id']}' data-entry='{$row['Entry_Id']}' data-name='{$row['Name']}'>
                    <td>{$row['Name']}</td>
                    <td>" . ($row['Exit_Time'] ?: '---') . "</td>
                    <td>" . ($row['Entry_Time'] ?: '---') . "</td>
                    <td><span class='status {$statusClass}'>{$statusText}</span></td>
                    <td>";

                if ($row['status'] == 0) {
                    echo "<button class='approve-btn' onclick=\"approveOuting('{$row['Student_Id']}')\">Approve</button>";
                } 
                else if ($row['status'] == 1) {
                    echo "<button class='approve-btn' onclick=\"markReturn('{$row['Entry_Id']}')\">Mark Returned</button>";
                }
                echo "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='6' style='text-align:center;'>No outing records found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>


<!-- JavaScript -->
<script>
function approveOuting(studentId) {
    if (!confirm("Approve outing for request?")) return;

    const fd = new FormData();
    fd.append("student_id", studentId);

    fetch("approve_outing.php", {
        method: "POST",
        body: fd
    })
    .then(res => res.text())
    .then(data => {
        if (data.trim() === "success") {
            alert("Outing Approved!");
            location.reload();
        } else {
            alert("Error approving outing!");
        }
    });
}


function markReturn(entryId) {

    const fd = new FormData();
    fd.append("entry_id", entryId);

    fetch("student_return.php", {
        method: "POST",
        body: fd
    })
    .then(res => res.text())
    .then(data => {

        console.log("DEBUG RESPONSE:", data);   // <<<< ADD THIS

        if (data.trim() === "success") {
            alert("Return Updated");
            location.reload();
        } else {
            alert("Server Response: " + data); // <<<< SHOW REAL ERROR
        }
    });
}

</script>

<footer>
    <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
</footer>

</body>
</html>
