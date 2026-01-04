<?php
session_start();
if(!isset($_SESSION["role"]) || $_SESSION["role"] !== "warden")
{
    header("Location:templates/login.html"); 
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

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Complaint Management | Hostel Management System</title>
  <link rel="stylesheet" href="css/complaint_management.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">

</head>

<body>

  <!-- Header -->
  <header class="header">
    <div class="logo">HMS</div>
    <nav class="nav">
      <a href="warden_dashboard.php">Home</a>
      <a href="student_records.php">Student Records</a>
      <a href="room_management.php">Room Management</a>
      <a href="complaint_management.php" class="active">Complaints</a>
      <a href="reports.php">Reports</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main class="main-content">
    <h1>Complaint Management</h1>

    <!-- Filter Section -->
     <form method = 'GET' id = 'filterForm'>
       <div class="filter-section">
         <label for="status-filter">Filter by Status:</label>
         <select name = "status" id="status-filter" onchange = "document.getElementById('filterForm').submit();">
           <option value="all" <?= (!isset($_GET['status']) || $_GET['status'] == 'all') ? 'selected' : ''?>> ALL</option>
           <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''?>> Pending</option>
           <option value="resolved"<?= (isset($_GET['status']) && $_GET['status'] == 'resolved') ? 'selected' : '' ?>> Resolved</option>
         </select>
       </div>

     </form>

    <!-- Complaint Table -->
    <?php
    include("connect.php");
    $status = $_GET['status'] ?? 'all';

    $complaint = "SELECT C.*, CONCAT(S.Fname, ' ', S.Lname) AS Name
                FROM Complaint C
                INNER JOIN Student S
                ON C.student_id = S.Student_Id
                WHERE S.Hostel_Id = '$hostel_id'";
    
    if($status == "pending") {
      $complaint .= " WHERE C.Status = 0";
    } elseif ($status == "resolved") {
      $complaint .= " WHERE C.Status = 1";
    }

    $complaint .= " ORDER BY Complaint_Id DESC";

    $result = mysqli_query($conn, $complaint);
    if(!$result)
    {
        die("SQL ERROR: " . mysqli_error($conn));
    }
    ?>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Complaint ID</th>
            <th>Student Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Status</th>
            <th>Applied On</th>
            <th>Resolved On</th>
            <th>Remarks</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="complaint-table">
            <?php while($row = mysqli_fetch_assoc($result)) {?>
          <tr>
                <td><?= htmlspecialchars($row['Complaint_Id']) ?></td>
                <td><?= htmlspecialchars($row['Name']) ?></td>
                <td><?= htmlspecialchars($row['Type']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>
                    <span class = "status <?= ($row['Status'] == 1) ? 'resolved' : 'pending'?>">
                        <?= ($row['Status'] == 1) ? 'Resolved' : 'Pending' ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($row['Applied_On'])?></td>
                <td><?= htmlspecialchars($row['Resolved_On']) ?></td>
                <td><?= htmlspecialchars($row['Remarks']) ?></td>
                <td>
                    <?php if($row['Status'] == 1) {?>
                        <button class="resolve-btn" disabled style="background: gray; cursor:not-allowed;">
                            Resolved
                        </button>
                    <?php } else {?>
                    <form method = "post" action = "update_complaints.php" style = "display:inline">
                        <input type = "hidden" name = "id" value = "<?= $row['Complaint_Id']?>">
                        <button type = "submit" name = "resolve" class = "resolve-btn">Resolve</button>
                    </form>
                    <?php } ?>
                    <button type = "button" class = "remark-btn" data-id = "<?= $row['Complaint_Id'] ?>">Add Remarks</button>
                </td>
          </tr>
          <?php }?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Popup Modal for Remark -->
  <div id="remarkModal" class="modal">
    <div class="modal-content">
      <h2>Add Remark</h2>
      <p id="complaint-id-text"></p>
      <textarea id="remarkText" placeholder="Enter your remark here..."></textarea>
      <div class="modal-actions">
        <button id="saveRemarkBtn">Save</button>
        <button id="closeModalBtn">Cancel</button>
      </div>
    </div>
  </div>

  <script>
    document.getElementById("status-filter").addEventListener("change", function () {
    const filterValue = this.value.toLowerCase();
    const rows = document.querySelectorAll("#complaint-table tr");

    rows.forEach(row => {
        const status = row.querySelector(".status").textContent.toLowerCase();
        row.style.display = (filterValue === "all" || status === filterValue) ? "" : "none";
    });
    });

    // Modal logic
    const modal = document.getElementById("remarkModal");
    const remarkText = document.getElementById("remarkText");
    const complaintIdText = document.getElementById("complaint-id-text");

    document.querySelectorAll(".remark-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        const id = btn.getAttribute("data-id");
        complaintIdText.textContent = "Complaint ID: " + id;
        remarkText.value = "";
        modal.style.display = "flex";
    });
    });

    document.getElementById("closeModalBtn").addEventListener("click", () => {
    modal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
    if (e.target === modal) modal.style.display = "none";
    });

    // Save remark AJAX
    document.getElementById("saveRemarkBtn").addEventListener("click", () => {
    const id = complaintIdText.textContent.split(": ")[1];
    const remark = remarkText.value.trim();

    if (remark === "") {
        alert("Please enter a remark!");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "save_remark.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (this.responseText.trim() === "success") {
            alert("Remark saved!");
            location.reload();
        } else {
            alert("Error saving remark.");
            console.log(this.responseText);
        }
    };

    xhr.send("id=" + id + "&remark=" + encodeURIComponent(remark));
    });
</script>

  <footer>
    <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
  </footer>
</body>

</html>