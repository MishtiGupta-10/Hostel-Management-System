<?php
session_start();

if(!isset($_SESSION["role"]) || $_SESSION["role"] !== "student")
{
    header("Location:templates/login.html"); 
    exit();
}

include("connect.php");

$student_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Room Details</title>
  <link rel="stylesheet" href="css/room-details.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <?php
    $query = "SELECT 
                R.Room_No,
                R.Room_Type,
                H.Name AS Hostel_Name,
                W.Name AS Warden_Name
            FROM Student S
            JOIN Room R ON S.Room_Id = R.Room_Id
            JOIN Hostel H ON S.Hostel_Id = H.Hostel_Id
            JOIN Warden W ON H.Warden_Id = W.Warden_Id
            WHERE S.Student_Id = '$student_id'";

    $result = mysqli_query($conn, $query);
    $details = mysqli_fetch_assoc($result);

    $roommatesQuery = "SELECT CONCAT(Fname, ' ', Lname) AS roommate
                        FROM Student
                        WHERE Room_Id = (SELECT Room_Id FROM Student WHERE Student_Id = '$student_id')
                        AND Student_Id != '$student_id'";

    $roommatesResult = mysqli_query($conn, $roommatesQuery);
    ?>
    
  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo">HMS</div>
    <div class="nav-links">
      <a href="student_dashboard.php">Home</a>
      <a href="logout.php" class="logout-btn">Logout</a>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container">
    <h1>Room Details</h1>

    <div class="room-info">
      <div class="info-item"><strong>Current Room No:</strong><?= $details['Room_No'] ?></div>
      <div class="info-item"><strong>Hostel Name:</strong><?= $details['Hostel_Name'] ?></div>
      <div class="info-item"><strong>Warden Name:</strong><?= $details['Warden_Name'] ?></div>
      <div class="info-item"><strong>Room Type:</strong><?= $details['Room_Type'] ?></div>
    </div>

    <h2>Your Roommates</h2>
    <ul class="roommate-list">
      <?php 
      if (mysqli_num_rows($roommatesResult) == 0) {
          echo "<li>No roommates assigned</li>";
      } else {
          while ($row = mysqli_fetch_assoc($roommatesResult)) {
              echo "<li>" . $row['roommate'] . "</li>";
          }
      }
      ?>
    </ul>


    <hr class="divider" />

  <script>
    document.getElementById("requestForm").addEventListener("submit", function (event) {
      event.preventDefault();
      alert("Your room change request has been submitted!");
      this.reset();
    });
  </script>
  <footer>
    <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
  </footer>
</body>

</html>