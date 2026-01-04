<?php
session_start();
if(!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location:templates/login.html");
    exit();
}

include("connect.php");

$Id = $_SESSION['user_id'];

$complaint = "SELECT Complaint_Id, Type, Status, Applied_On, Resolved_On, Remarks 
              FROM Complaint 
              WHERE Student_Id = $Id";

$result = mysqli_query($conn, $complaint);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Page</title>
    <link rel="stylesheet" href="css/complaint.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">HMS</div>
        <ul class="nav-links">
            <li><a href="student_dashboard.php">Home</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Complaint Form</h1>

        <div class="form-container">
            <form id="complaintForm" method="POST" action="save_complaint.php">
                <label for="type">Complaint Type</label>
                <select id="type" name="type" required>
                    <option value="">Select Type</option>
                    <option value="Room Maintenance">Room Maintenance</option>
                    <option value="Mess Food">Mess Food</option>
                    <option value="Electricity">Electricity</option>
                    <option value="Water Supply">Water Supply</option>
                    <option value="Other">Other</option>
                </select>

                <label for="desc">Description</label>
                <textarea id="desc" name="desc" rows="4"
                    placeholder="Describe your complaint..." required></textarea>

                <input type="hidden" name="student_id" value="<?= $Id ?>">

                <button type="submit" class="btn-submit">Submit Complaint</button>
            </form>
        </div>

        <h2>Your Complaint Records</h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Resolved On</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Complaint_Id']) ?></td>
                        <td><?= htmlspecialchars($row['Type']) ?></td>
                        <td><?= $row['Status'] == 1 ? 'Resolved' : 'Pending' ?></td>
                        <td><?= htmlspecialchars($row['Applied_On']) ?></td>
                        <td><?= htmlspecialchars($row['Resolved_On'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['Remarks'] ?? '—') ?></td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6'>No complaints found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
    </footer>

</body>
</html>
