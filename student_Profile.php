<?php
session_start();
if(!isset($_SESSION["role"]) || $_SESSION["role"] !== "student")
{
    header("Location:templates/login.html"); 
    exit();
}

include("connect.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Profile</title>
  <link rel="stylesheet" href="css/studentProfile.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">

</head>


<body>
  <?php if (isset($_GET['updated'])): ?>
  <script>
    alert("Updated successfully!");
    window.location.href = "student_profile.php";
  </script>
  <?php endif; ?>

  

  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo">HMS</div>
    <div class="nav-buttons">
      <button class="nav-btn home-btn" onclick="window.location.href='student_dashboard.php'">Home</button>
      <button class="nav-btn logout-btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>
  </nav>

  <?php
  $Id = $_SESSION['user_id'];
  $student = "SELECT 
	          Student_Id,
              CONCAT(Fname, ' ', Lname) AS Name,
              timestampdiff(YEAR, Bdate, CURDATE()) AS Age,
              contact_info AS Contact,
              gender,
              Bdate,
              Email
              FROM Student
              WHERE Student_Id = $Id";

    $result = mysqli_query($conn, $student);
    if($result) {
        $row = mysqli_fetch_assoc($result);
  ?>

  <!-- Profile Section -->
  <div class="profile-container">
    <h1>Student Profile</h1>

    <div class="profile-card">
      <table class="profile-table">
        <tr>
          <th>Name</th>
          <td><?= $row['Name']; ?></td>
        </tr>
        <tr>
            <th>Age</th>
            <td><?= $row['Age']; ?></td>
        </tr>
        <tr>
          <th>Contact</th>
          <td>
            <span id="student-contact"><?= $row['Contact']; ?></span>
            <button class="edit-btn" onclick="openPopup('contact')" name = "edit-btn">Edit</button>
          </td>
        </tr>
        <tr>
          <th>Gender</th>
          <td><?= $row['gender']; ?></td>
        </tr>
        <tr>
          <th>Date of Birth</th>
          <td><?= $row['Bdate']; ?></td>
        </tr>
        <tr>
          <th>Email</th>
          <td>
            <span id="student-email"><?= $row['Email']; ?></span>
            <button class="edit-btn" onclick="openPopup('email')" name = "edit-btn">Edit</button>
          </td>
        </tr>
      </table>
    <?php }?>
    </div>
  </div>

  <?php
  
  ?>

  <!-- Popup Modal -->
  <div class="popup" id="popupForm">
    <div class="popup-content">
      <span class="close-btn" id="closePopupBtn">&times;</span>
      <h2 id="popupTitle">Update</h2>

      <!-- Form -->
      <form id="otpForm" action = "update_profile.php" method = "POST">
        <input type = "hidden" name = "field" id = "fieldInput">

        <label id="updateLabel" for="newValue">Enter New Value</label>
        <input type="text" name = "value" id="newValue" placeholder="Enter new value" required />

        <button type="submit" class="send-otp-btn" id="sendOtpBtn">Save</button>
      </form>
    </div>
  </div>


  <!-- Script -->
  <script>
    const popup = document.getElementById("popupForm");
    const closeBtn = document.getElementById("closePopupBtn");
    const popupTitle = document.getElementById("popupTitle");
    const newValueInput = document.getElementById("newValue");

    function openPopup(type) {
      currentField = type;
      popup.style.display = "flex";

      document.getElementById("fieldInput").value = type;
      popupTitle.textContent =
        type === "contact" ? "Update Contact Number" : "Update Email";
      newValueInput.type = type === "contact" ? "tel" : "email";
      newValueInput.placeholder =
        type === "contact" ? "Enter new contact number" : "Enter new email";
    }

    closeBtn.addEventListener("click", () => {
      popup.style.display = "none";
      clearInterval(timerInterval);
    });

    window.addEventListener("click", (e) => {
      if (e.target === popup) {
        popup.style.display = "none";
        clearInterval(timerInterval);
      }
    });
  </script>
  <footer>
    <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
  </footer>
</body>

</html>