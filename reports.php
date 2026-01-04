<?php
session_start();
if(!isset($_SESSION["role"]) || $_SESSION["role"] !== "warden")
{
    header("Location:templates/login.html"); 
    exit();
}

include("connect.php");
$warden_id = $_SESSION['user_id'];

$hostelQuery = "
    SELECT Hostel_Id 
    FROM Hostel
    WHERE Warden_Id = '$warden_id'
    LIMIT 1";
$hostelResult = mysqli_query($conn, $hostelQuery);
$wardenData = mysqli_fetch_assoc($hostelResult);

$hostel_id = $wardenData['Hostel_Id'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reports | Hostel Management System</title>
  <link rel="stylesheet" href="css/reports.css" />
</head>

<body>

<header class="navbar">
    <div class="logo">HMS</div>
    <nav>
      <ul class="nav-links">
        <li><a href="warden_dashboard.php">Home</a></li>
        <li><a href="student_records.php">Student Records</a></li>
        <li><a href="room_management.php">Rooms</a></li>
        <li><a href="complaint_management.php">Complaints</a></li>
        <li><a href="reports.php" class="active">Reports</a></li>
        <li><a href="logout.php" class="logout-btn">Logout</a></li>
      </ul>
    </nav>
</header>

<main class="container">

<h1>📊 Hostel Reports</h1>

<div class="report-controls">
    <form method="GET">
        <label>Select Report Type:</label>
        <select name="type" required>
            <option value="">-- Select --</option>
            <option value="occupancy" 
              <?= (isset($_GET['type']) && $_GET['type']=="occupancy") ? "selected" : "" ?>>
              Occupancy Report
            </option>

            <option value="complaints"
              <?= (isset($_GET['type']) && $_GET['type']=="complaints") ? "selected" : "" ?>>
              Complaints Report
            </option>
        </select>

        <button class="btn generate-btn" type="submit">Generate Report</button>
        <button class="btn clear-btn" type="button" onclick="window.location='reports.php'">Clear</button>
    </form>
</div>

<div class="report-content" id="report-content">

<?php
if (!isset($_GET["type"])) {
    echo "<p class='placeholder'>Select a report type and click \"Generate Report\".</p>";
}


if (isset($_GET["type"]) && $_GET["type"] === "occupancy") {

    $q = "SELECT 
            H.Name AS Hostel,
            COUNT(R.Room_Id) AS TotalRooms,
            SUM(R.Occupied) AS TotalOccupied,
            SUM(R.Capacity - R.Occupied) AS TotalAvailable
          FROM Room R
          JOIN Hostel H ON H.Hostel_Id = R.Hostel_Id
          WHERE R.Hostel_Id = '$hostel_id'
          GROUP BY H.Hostel_Id";

    $res = mysqli_query($conn, $q);

    echo "<h2>🏠 Occupancy Report</h2>";

    if (mysqli_num_rows($res) > 0) {
        echo "<table>
                <thead>
                  <tr>
                    <th>Hostel</th>
                    <th>Total Rooms</th>
                    <th>Occupied Beds</th>
                    <th>Available Beds</th>
                  </tr>
                </thead>
                <tbody>";

        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>
                    <td>{$row['Hostel']}</td>
                    <td>{$row['TotalRooms']}</td>
                    <td>{$row['TotalOccupied']}</td>
                    <td>{$row['TotalAvailable']}</td>
                  </tr>";
        }

        echo "</tbody></table>";
    } 
    else {
        echo "<p>No data available.</p>";
    }
}


if (isset($_GET["type"]) && $_GET["type"] === "complaints") {

  $q = "SELECT 
          C.Type,
          SUM(CASE WHEN C.Status = 0 THEN 1 ELSE 0 END) AS Pending,
          SUM(CASE WHEN C.Status = 1 THEN 1 ELSE 0 END) AS Resolved,
          COUNT(*) AS Total
        FROM Complaint C
        INNER JOIN Student S ON C.student_id = S.Student_Id
        WHERE S.Hostel_Id = '$hostel_id'
        GROUP BY C.Type";


    $res = mysqli_query($conn, $q);

    echo "<h2>📨 Complaints Report</h2>";

    if (mysqli_num_rows($res) > 0) {
        echo "<table>
                <thead>
                  <tr>
                    <th>Complaint Type</th>
                    <th>Pending</th>
                    <th>Resolved</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>";

        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>
                    <td>{$row['Type']}</td>
                    <td>{$row['Pending']}</td>
                    <td>{$row['Resolved']}</td>
                    <td>{$row['Total']}</td>
                  </tr>";
        }

        echo "</tbody></table>";
    } 
    else {
        echo "<p>No complaints found.</p>";
    }
}
?>

</div>

</main>

<footer>
    <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
</footer>

</body>
</html>
