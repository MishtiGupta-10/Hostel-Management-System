<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: templates/login.html");
    exit();
}

include("connect.php");

$student_id = $_SESSION["user_id"];

$getHostel = "SELECT Hostel_Id FROM Student WHERE Student_Id = '$student_id' LIMIT 1";
$hostelRes = mysqli_query($conn, $getHostel);
$studentHostel = mysqli_fetch_assoc($hostelRes)["Hostel_Id"];

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$noticeQuery = "SELECT 
                    N.notice_id, 
                    N.title, 
                    N.description, 
                    N.date,
                    W.Name AS posted_by
                FROM Notice N
                LEFT JOIN Warden W ON N.warden_id = W.Warden_Id
                WHERE N.hostel_id = '$studentHostel'";

if (!empty($search)) {
    $noticeQuery .= " AND (N.title LIKE '%$search%' OR N.description LIKE '%$search%')";
}

$noticeQuery .= " ORDER BY N.date DESC";

$noticeResult = mysqli_query($conn, $noticeQuery);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notice Board</title>
  <link rel="stylesheet" href="css/notice.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<!-- Navbar -->
<nav class="navbar">
  <div class="logo">Notice Board</div>

  <div class="nav-links">
    <button class="nav-btn home-btn" onclick="window.location.href='student_dashboard.php'">Home</button>
    <button class="nav-btn logout-btn" onclick="window.location.href='logout.php'">Logout</button>
  </div>
</nav>

<!-- Container -->
<div class="container">
  <h1>Latest Notices</h1>

  <!-- Filters -->
<div class="filters">
  <form method="GET" action="notice.php" class="filter-form">

    <input type="text" name="search" class="search-input"
           placeholder="Search by Title or Description..."
           value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">

    <button type="submit" class="search-btn">Search</button>

    <a href="notice.php" class="clear-btn">Clear</a>
  </form>
</div>


  <!-- Notice Table -->
  <table id="noticeTable">
    <thead>
      <tr>
        <th>Date</th>
        <th>Title</th>
        <th>Description</th>
        <th>Posted By</th>
      </tr>
    </thead>

    <tbody>
      <?php
      if ($noticeResult && mysqli_num_rows($noticeResult) > 0) {
          while ($row = mysqli_fetch_assoc($noticeResult)) {

              $displayDate = date('d-m-Y', strtotime($row['date']));

              echo "
              <tr>
                <td data-label='Date'>{$displayDate}</td>
                <td data-label='Title'>" . htmlspecialchars($row['title']) . "</td>
                <td data-label='Description'>" . htmlspecialchars($row['description']) . "</td>
                <td data-label='Posted By'>" . htmlspecialchars($row['posted_by']) . "</td>
              </tr>";
          }
      } else {
          echo "<tr><td colspan='4' style='text-align:center;'>No notices found for your hostel.</td></tr>";
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
