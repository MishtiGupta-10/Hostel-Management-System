<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: templates/login.html");
    exit();
}

include("connect.php");

$hostelQuery = "SELECT COUNT(*) AS total_hostels FROM Hostel";
$hostelRes = mysqli_query($conn, $hostelQuery);
$totalHostels = mysqli_fetch_assoc($hostelRes)['total_hostels'];

$wardenQuery = "SELECT COUNT(*) AS total_wardens FROM Warden";
$wardenRes = mysqli_query($conn, $wardenQuery);
$totalWardens = mysqli_fetch_assoc($wardenRes)['total_wardens'];

$studentQuery = "SELECT COUNT(*) AS total_students FROM Student";
$studentRes = mysqli_query($conn, $studentQuery);
$totalStudents = mysqli_fetch_assoc($studentRes)['total_students'];

$complaintQuery = "SELECT COUNT(*) AS pending_complaints FROM Complaint WHERE Status = 0";
$complaintRes = mysqli_query($conn, $complaintQuery);
$pendingComplaints = mysqli_fetch_assoc($complaintRes)['pending_complaints'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard | HMS</title>
  <link rel="stylesheet" href="css/admin_dashboard.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<header>
  <nav class="navbar">
    <div class="logo">HMS</div>
    <ul class="nav-links">
      <!-- <li><a href="admin_dashboard.php">Dashboard</a></li> -->
      <li><a href="templates/index.html">Home</a></li>
      <li><a href="logout.php" class="btn">Logout</a></li>
    </ul>
  </nav>
</header>

<div class="container">


  <h2 id="greeting"></h2>

  <!-- Summary Section -->
  <div class="summary-cards">
    <div class="card">
      <h3>Total Hostels</h3>
      <p><?= $totalHostels ?></p>
    </div>

    <div class="card">
      <h3>Total Wardens</h3>
      <p><?= $totalWardens ?></p>
    </div>

    <div class="card">
      <h3>Total Students</h3>
      <p><?= $totalStudents ?></p>
    </div>

    <div class="card">
      <h3>Pending Complaints</h3>
      <p><?= $pendingComplaints ?></p>
    </div>
  </div>

  <!-- Navigation Buttons -->
  <div class="nav-buttons">
    <a href="manage_users.php" class="btn">Manage Users</a>
    <a href="manage_hostel.php" class="btn">Manage Hostels</a>
    <a href="manage_fees.php" class="btn">Manage Fees</a>
    <a href="admin_reports.php" class="btn">Reports & Analytics</a>
  </div>
</div>

<footer>
  <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
</footer>

<script>
  // Dynamic greeting
  const greeting = document.getElementById("greeting");
  const hour = new Date().getHours();

  if (hour < 12) greeting.textContent = "Good Morning ☀️, Admin!";
  else if (hour < 18) greeting.textContent = "Good Afternoon 🌤️, Admin!";
  else greeting.textContent = "Good Evening 🌙, Admin!";
</script>

</body>
</html>
