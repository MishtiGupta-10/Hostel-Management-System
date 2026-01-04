<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "warden") {
    header("Location: templates/login.html");
    exit();
}

include("connect.php");

$warden_id = $_SESSION['user_id'];

$hostelQuery = "SELECT Hostel_Id 
                FROM Hostel 
                WHERE Warden_Id = '$warden_id' 
                LIMIT 1";
$hostelResult = mysqli_query($conn, $hostelQuery);
$wardenData = mysqli_fetch_assoc($hostelResult);

$hostel_id = $wardenData['Hostel_Id'];

$studentCountQuery = "SELECT COUNT(*) AS Total_Students 
                      FROM Student 
                      WHERE Hostel_Id = '$hostel_id'";
$studentCountResult = mysqli_query($conn, $studentCountQuery);
$studentCount = mysqli_fetch_assoc($studentCountResult)['Total_Students'];

$complaintCountQuery = "SELECT COUNT(*) AS Pending_Complaints
                        FROM Complaint C
                        JOIN Student S ON C.Student_Id = S.Student_Id
                        WHERE S.Hostel_Id = '$hostel_id'
                        AND C.Status = 0";
$complaintCountResult = mysqli_query($conn, $complaintCountQuery);
$pendingComplaints = mysqli_fetch_assoc($complaintCountResult)['Pending_Complaints'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warden Dashboard</title>
    <link rel="stylesheet" href="css/warden_dashboard.css">
</head>

<body>

<nav class="navbar">
    <div class="logo">HMS</div>
    <ul class="nav-links">
        <li><a href="warden_dashboard.php" class="nav-btn">Home</a></li>
        <li><a href="logout.php" class="logout-btn">Logout</a></li>
    </ul>
</nav>

<section class="dashboard">
    <h1>Welcome, Warden</h1>

    <div class="summary-cards">

        <div class="card total-students">
            <h2>Total Students</h2>
            <p><?php echo $studentCount; ?></p>
        </div>

        <div class="card complaints">
            <h2>Pending Complaints</h2>
            <p><?php echo $pendingComplaints; ?></p>
        </div>

        <div class="card outing">
            <h2>Outing Requests</h2>
            <p>5</p>
        </div>

    </div>
</section>

<div class="bottom-menu">
    <a href="student_records.php" class="menu-btn">Student Records</a>
    <a href="room_management.php" class="menu-btn">Room Management</a>
    <a href="complaint_management.php" class="menu-btn">Complaints</a>
    <a href="reports.php" class="menu-btn">Reports</a>
    <a href="outing.php" class="menu-btn">Outing Request</a>
    <a href="outing_history.php" class="menu-btn">Outing History</a>
    <a href="warden_notice.php" class="menu-btn">Notices</a>
</div>

<footer>
    <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
</footer>

<?php mysqli_close($conn); ?>
</body>

</html>
