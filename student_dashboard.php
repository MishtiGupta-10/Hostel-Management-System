<?php
session_start();

if(!isset($_SESSION["role"]) || $_SESSION["role"] !== "student")
{
    header("Location:templates/login.html"); //redirecting if  not logged in 
    exit();
}

include("connect.php");

$student_id = $_SESSION['user_id'];

$query = "  SELECT concat(S.Fname, ' ', S.Lname) AS Name,R.Room_No 
            FROM Student AS S
            Join Room AS R ON S.Room_Id = R.Room_Id
            WHERE S.Student_Id = '$student_id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

$presentQuery = "SELECT COUNT(DISTINCT DATE(time)) AS present_days
                 FROM Attendance
                 WHERE Student_Id = '$student_id';";
$presentRes = mysqli_query($conn, $presentQuery);
$presentData = mysqli_fetch_assoc($presentRes);
$present_days = $presentData['present_days'] ?? 0;

// 2. Get first attendance date
$firstDateQuery = "
    SELECT MIN(DATE(time)) AS first_day
    FROM Attendance
    WHERE Student_Id = '$student_id'
";
$firstDateRes = mysqli_query($conn, $firstDateQuery);
$firstData = mysqli_fetch_assoc($firstDateRes);
$first_day = $firstData['first_day'];

// If no attendance exists yet → percentage = 0
if ($first_day == null) {
    $attendance_percent = 0;
} else {
    // 3. Calculate total days
    $totalDaysQuery = "SELECT DATEDIFF(CURDATE(), '$first_day') + 1 AS total_days";
    $totalDaysRes = mysqli_query($conn, $totalDaysQuery);
    $totalDaysData = mysqli_fetch_assoc($totalDaysRes);
    $total_days = $totalDaysData['total_days'];

    // 4. Calculate percentage
    $attendance_percent = ($present_days / $total_days) * 100;
    $attendance_percent = number_format($attendance_percent, 1);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard | Hostel Management System</title>
  <link rel="stylesheet" href="css/student_dashboard.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
</head>

<body>
  <!-- Header -->
  <header class="header">
  <div class="logo">HMS</div>

  <nav class="nav-links">
    <button class="nav-btn logout" onclick="window.location.href='logout.php'">Logout</button>
  </nav>
</header>


  <div class="dashboard">
    <!-- Main Content -->
    <main class="main-content">
      <h1>Welcome, <?php echo $student["Name"];?></h1>
      <p class="subtext">Here's an overview of your hostel details and recent updates.</p>

      <!-- Cards -->
      <div class="cards">
        <div class="card">
          <h3>Room No</h3>
          <p><?= htmlspecialchars($student["Room_No"]);?></p>
        </div>
        <div class="card">
          <h3>Attendance</h3>
          <p><?= $attendance_percent ?>%</p>
        </div>
        <div class="card">
          <h3>Pending Outings</h3>
          <p>2</p>
        </div>
      </div>

      <!-- Attendance Section -->
       <?php
        $attendanceQuery = "SELECT DATE(time) AS day
                            FROM Attendance
                            WHERE Student_Id = '$student_id'
                            AND DATE(time) >= DATE_SUB(CURDATE(), INTERVAL 5 DAY)
                            ORDER BY day DESC";

        $attendanceRes = mysqli_query($conn, $attendanceQuery);

        $presentDays = [];
        while ($row = mysqli_fetch_assoc($attendanceRes)) {
            $presentDays[] = $row['day'];
        }

        $last5Days = [];
        for ($i = 0; $i < 5; $i++) {
            $last5Days[] = date('Y-m-d', strtotime("-$i days"));
        }

       ?>
      <section class="attendance-section">
        <div class="edit-profile-btn-container">
          <button class="edit-profile-btn" onclick="window.location.href='student_Profile.php'">View Profile</button>
          <button class="edit-profile-btn" onclick="window.location.href='room_details.php'">Room Details</button>
        </div>

        <h2>Mark Your Attendance</h2>
        <div class="attendance-box">
          <div class="attendance-info">
            <p><strong>Today's Date:</strong><?= date("d M Y"); ?></p>
            <p><strong>Status:</strong> Not Marked</p>
          </div>
          <button class="mark-btn" onclick = "markAttendance()">Mark Attendance</button>
        </div>

        <div class="attendance-history">
          <h3>Recent Attendance History</h3>
          <table>
            <thead>
              <tr>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
            <?php
            foreach ($last5Days as $day) {
                $formatted = date("d M Y", strtotime($day));

                if (in_array($day, $presentDays)) {
                    echo "
                    <tr>
                        <td>$formatted</td>
                        <td><span class='status present'>Present</span></td>
                    </tr>";
                } 
                else {
                    echo "
                    <tr>
                        <td>$formatted</td>
                        <td><span class='status absent'>Absent</span></td>
                    </tr>";
                }
            }
            ?>
            </tbody>

          </table>
        </div>
      </section>

      <!-- Outing Requests -->
      <section class="table-container">
        <h2>Outing Requests</h2>

        <?php
        // fetch outings for current student
        $outings_q = "SELECT Entry_Id, Exit_Time, Entry_Time, status
                      FROM Home_Entry
                      WHERE Student_Id = '$student_id'
                      ORDER BY Entry_Id DESC";
        $outings_res = mysqli_query($conn, $outings_q);
        ?>

        <table>
          <thead>
            <tr>
              <th>Entry ID</th>
              <th>Exit Time</th>
              <th>Entry Time</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            <?php if ($outings_res && mysqli_num_rows($outings_res) > 0): ?>
              <?php while ($o = mysqli_fetch_assoc($outings_res)): ?>
                <?php
                  if (!empty($o['Entry_Time']) && !empty($o['Exit_Time'])) {
                    $status_label = "Completed";
                    $status_class = "completed";
                  } elseif (!empty($o['Exit_Time']) && empty($o['Entry_Time'])) {
                    $status_label = "Approved (Outside)";
                    $status_class = "outside";
                  } elseif (empty($o['Exit_Time']) && empty($o['Entry_Time']) && ($o['status'] === "0" || $o['status'] === 0 || $o['status'] === null)) {
                    $status_label = "Pending";
                    $status_class = "pending";
                  } else {
                    $status_label = "Unknown";
                    $status_class = "unknown";
                  }

                  $exit_display = !empty($o['Exit_Time']) ? htmlspecialchars($o['Exit_Time']) : '---';
                  $entry_display = !empty($o['Entry_Time']) ? htmlspecialchars($o['Entry_Time']) : '---';
                ?>

                <tr>
                  <td><?= htmlspecialchars($o['Entry_Id']) ?></td>
                  <td><?= $exit_display ?></td>
                  <td><?= $entry_display ?></td>
                  <td><span class="status <?= $status_class ?>"><?= $status_label ?></span></td>
                  <td>
                    <?php if ($status_label === "Pending"): ?>
                      <form method="POST" action="cancel_outing.php" style="display:inline" onsubmit="return confirm('Cancel this outing request?');">
                        <input type="hidden" name="entry_id" value="<?= htmlspecialchars($o['Entry_Id']) ?>">
                        <button type="submit" class="cancel-btn">Cancel</button>
                      </form>
                    <?php else: ?>
                      <span style="color:#777;font-size:0.9rem">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" style="text-align:center;">No outing records found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <!-- Bottom Buttons (3 in a row) -->
      <div class="bottom-buttons">
        <button onclick="openModal('complaintModal')">File a Complaint</button>
        <button onclick="requestOuting()">Request Outing</button>
        <button onclick="window.location.href='notice.php'">View All Notices</button>
      </div>


  <!-- Complaint Modal -->
  <div id="complaintModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('complaintModal')">&times;</span>
      <h2>File a Complaint</h2>
      <form method = "POST" action = "save_student_complaint.php">
        <label for="complaintType">Complaint Type</label>
        <select id="complaintType" name = "type" required>
          <option value="">Select Type</option>
          <option value = "Room Maintenance">Room Maintenance</option>
          <option value = "Mess Food">Mess Food</option>
          <option value = "Water">Water</option>
          <option value = "Electricity">Electricity</option>
          <option>Other</option>
        </select>

        <label for="complaintDesc">Description</label>
        <textarea id="complaintDesc" name = "desc" placeholder="Describe your issue..." required></textarea>

        <input type = "hidden" name = "student_id" value = "<?= $student_id ?>">

        <button type="submit">Submit Complaint</button>
      </form>
    </div>
  </div>


  <script>
    function openModal(id) {
      document.getElementById(id).style.display = "flex";
    }

    function closeModal(id) {
      document.getElementById(id).style.display = "none";
    }

    window.onclick = function (event) {
      const modals = document.querySelectorAll(".modal");
      modals.forEach((modal) => {
        if (event.target === modal) modal.style.display = "none";
      });
    };

    function requestOuting() {
        if (!confirm("Do you want to request an outing?")) return;

        const fd = new FormData();
        fd.append("request_outing", "1");

        fetch("request_outing.php", {
            method: "POST",
            body: fd
        })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === "success") {
                alert("Outing request submitted!");
                location.reload();
            } else {
                alert("Error submitting outing request");
                console.log(data);
            }
        });
    }

    function markAttendance() {
        if (!confirm("Are you sure you want to mark your attendance?")) return;

        const fd = new FormData();
        fd.append("mark", "1");

        fetch("mark_attendance.php", {
            method: "POST",
            body: fd
        })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === "success") {
                alert("Attendance marked successfully!");
                location.reload();
            } else {
                alert("Error marking attendance!");
                console.log(data);
            }
        });
    }


  </script>
  <footer>
    <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
  </footer>
</body>

</html>