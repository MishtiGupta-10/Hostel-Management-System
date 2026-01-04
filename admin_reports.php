<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: templates/login.html");
    exit();
}

include("connect.php");


// Total Students
$q1 = "SELECT COUNT(*) AS total FROM Student";
$r1 = mysqli_query($conn, $q1);
$totalStudents = mysqli_fetch_assoc($r1)['total'];

// Total Hostels
$q2 = "SELECT COUNT(*) AS total FROM Hostel";
$r2 = mysqli_query($conn, $q2);
$totalHostels = mysqli_fetch_assoc($r2)['total'];

// Total Complaints
$q3 = "SELECT COUNT(*) AS total FROM Complaint";
$r3 = mysqli_query($conn, $q3);
$totalComplaints = mysqli_fetch_assoc($r3)['total'];

$statusQuery = "
    SELECT 
        SUM(CASE WHEN Status = 0 THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN Status = 2 THEN 1 ELSE 0 END) AS inProgress,
        SUM(CASE WHEN Status = 1 THEN 1 ELSE 0 END) AS resolved
    FROM Complaint
";
$statusRes = mysqli_query($conn, $statusQuery);
$status = mysqli_fetch_assoc($statusRes);

// Fees Collected
$q4 = "SELECT SUM(Amount) AS fees FROM Fee WHERE Status = 1";
$r4 = mysqli_query($conn, $q4);
$feesCollected = mysqli_fetch_assoc($r4)['fees'] ?? 0;


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Reports | HMS</title>
  <link rel="stylesheet" href="css/admin_reports.css" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<header>
  <nav class="navbar">
    <div class="logo">HMS</div>
    <ul class="nav-links">
      <li><a href="admin_dashboard.php">Dashboard</a></li>
      <li><a href="manage_hostel.php">Hostels</a></li>
      <li><a href="manage_users.php">Users</a></li>
      <li><a href="admin_complaints.php">Complaints</a></li>
      <li><a href="manage_fees.php">Fees</a></li>
      <li><a href="logout.php" class="btn">Logout</a></li>
    </ul>
  </nav>
</header>

<div class="container">

  <h2>System Overview</h2>

  <!-- Summary Cards -->
  <div class="summary-cards">
    <div class="card">
      <h3>Total Students</h3>
      <p><?= $totalStudents ?></p>
    </div>

    <div class="card">
      <h3>Total Hostels</h3>
      <p><?= $totalHostels ?></p>
    </div>

    <div class="card">
      <h3>Total Complaints</h3>
      <p><?= $totalComplaints ?></p>
    </div>

    <div class="card">
      <h3>Fees Collected (₹)</h3>
      <p><?= number_format($feesCollected) ?></p>
    </div>
  </div>

  <!-- Chart Section -->
  <h2>Complaint Status Overview</h2>
  <canvas id="complaintChart"></canvas>

  <!-- Download Button -->
  <div class="button-section">
    <button class="btn" id="downloadBtn">Download Summary</button>
  </div>

</div>

<footer>
  © 2025 Hostel Management System | Admin Panel
</footer>

<script>
const data = {
  pending: <?= $status['pending'] ?>,
  inProgress: <?= $status['inProgress'] ?>,
  resolved: <?= $status['resolved'] ?>
};

const canvas = document.getElementById("complaintChart");
const ctx = canvas.getContext("2d");

canvas.width = 500;
canvas.height = 300;

const colors = ["#ff4d4d", "#ffcc00", "#28a745"];
const labels = ["Pending", "In Progress", "Resolved"];
const values = [data.pending, data.inProgress, data.resolved];

const total = values.reduce((a, b) => a + b, 0);
let startAngle = 0;

values.forEach((value, i) => {
  const slice = (value / total) * 2 * Math.PI;
  ctx.beginPath();
  ctx.moveTo(250, 150);
  ctx.arc(250, 150, 100, startAngle, startAngle + slice);
  ctx.closePath();
  ctx.fillStyle = colors[i];
  ctx.fill();
  startAngle += slice;
});

// Labels
ctx.font = "16px Poppins";
labels.forEach((label, i) => {
  ctx.fillStyle = colors[i];
  ctx.fillRect(400, 80 + i * 30, 15, 15);
  ctx.fillStyle = "#333";
  ctx.fillText(`${label}: ${values[i]}`, 420, 93 + i * 30);
});

document.getElementById("downloadBtn").addEventListener("click", () => {
  const report = `
------ HMS ADMIN REPORT ------
Date: ${new Date().toLocaleString()}

📊 Summary:
- Total Students: <?= $totalStudents ?>
- Total Hostels: <?= $totalHostels ?>
- Total Complaints: <?= $totalComplaints ?>
- Fees Collected: ₹<?= number_format($feesCollected) ?>


📈 Complaints Breakdown:
- Pending: ${data.pending}
- In Progress: ${data.inProgress}
- Resolved: ${data.resolved}

-----------------------------
  `;
  const blob = new Blob([report], { type: "text/plain" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "HMS_Report.txt";
  link.click();
});
</script>

</body>
</html>
