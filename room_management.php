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
  <title>Room Management | Hostel Management System</title>
  <link rel="stylesheet" href="css/room_management.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap"
    rel="stylesheet">

  <style>
    /* Popup Background */
    .popup-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.55);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 999;
    }

    /* Popup Box */
    .popup-box {
      width: 420px;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.25);
      animation: pop 0.25s ease;
    }

    @keyframes pop {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }

    .popup-box h2 {
      margin-bottom: 15px;
      font-size: 22px;
      text-align: center;
    }

    .popup-box select, .popup-box input {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border-radius: 6px;
      border: 1px solid #aaa;
      font-size: 15px;
    }

    .popup-btns {
      display: flex;
      justify-content: space-between;
      margin-top: 15px;
    }

    .save-btn, .close-btn {
      padding: 10px 16px;
      border: none;
      cursor: pointer;
      font-size: 15px;
      border-radius: 6px;
    }

    .save-btn {
      background: #4CAF50;
      color: white;
    }

    .close-btn {
      background: #e74c3c;
      color: white;
    }

    .edit-btn {
      margin-top: 20px;
      padding: 10px 20px;
      cursor: pointer;
      background: #393E46;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
    }

    .controls {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .controls input {
        padding: 10px;
        width: 220px;
        border: 1px solid #aaa;
        border-radius: 6px;
        font-size: 14px;
    }

    .search-btn {
        background: #393E46;
        color: white;
        padding: 10px 18px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }

    .search-btn:hover {
        background: #4b525c;
    }

    .clear-btn {
        background: #e74c3c;
        color: white;
        padding: 10px 18px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }

    .clear-btn:hover {
        background: #c0392b;
    }

  </style>

</head>

<body>
  <header class="header">
    <div class="logo">HMS</div>
    <nav class="nav">
      <a href="warden_dashboard.php" class="nav-btn">Home</a>
      <a href="student_records.php" class="nav-btn">Student Records</a>
      <a href="complaint_management.php" class="nav-btn">Complaints</a>
      <a href="reports.php" class="nav-btn">Reports</a>
      <a href="logout.php" class="nav-btn logout">Logout</a>
    </nav>
  </header>

  <main class="main-content">
    <h1>Room Management</h1>

   <form method="GET" class="controls">
        <input 
            type="text" 
            name="room" 
            id="searchRoom" 
            placeholder="Search by Room No..." 
            value="<?php echo isset($_GET['room']) ? $_GET['room'] : ''; ?>"
        >

        <input 
            type="text" 
            name="type" 
            id="searchType" 
            placeholder="Search by Room Type... (Single/Double/Triple)" 
            value="<?php echo isset($_GET['type']) ? $_GET['type'] : ''; ?>"
        >

        <button type="submit" class = "search-btn">Search</button>
        <button type="button" class = "clear-btn" onclick="window.location='room_management.php'">Clear</button>
    </form>



    <table id="roomTable">
      <thead>
        <tr>
          <th>Room No</th>
          <th>Room Type</th>
          <th>Capacity</th>
          <th>Occupied</th>
          <th>Available Slots</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $room_no = $_GET['room'] ?? '';
        $type = $_GET['type'] ?? '';

        $room = "SELECT Room_No, Capacity, Occupied, (Capacity - Occupied) AS Available, Room_Type AS Type
                FROM Room
                WHERE Hostel_Id = '$hostel_id'";

        if(!empty($room_no)) {
            $room .= " AND Room_No LIKE '%$room_no%' ";
        }

        if(!empty($type)) {
            $room .= " AND LOWER(Room_Type) LIKE LOWER('%$type%') ";

        }

        $room .= " ORDER BY Room_No ASC ";

        $result = mysqli_query($conn, $room);
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
        ?>
        <tr>
            <td><?= htmlspecialchars($row['Room_No'])?></td>
            <td><?= htmlspecialchars($row['Type'])?></td>
            <td><?= htmlspecialchars($row['Capacity'])?></td>
            <td><?= htmlspecialchars($row['Occupied'])?></td>
            <td><?= htmlspecialchars($row['Available'])?></td>
        </tr>
        <?php }
        } ?>
      </tbody>
    </table>

    <!-- Edit Button -->
  </main>

  <!-- Popup Modal -->
  <div class="popup-overlay" id="popup">
    <div class="popup-box">
      <h2>Edit Room</h2>

      <label>Select Room No</label>
      <select id="roomSelect"></select>

      <label>Capacity</label>
      <input type = "number" id = "editCap" min = "1">

      <label>Occupied</label>
      <input type="number" id="editOccupied" min="0">

      <label>Available Slots</label>
      <input type="number" id="editAvailable" disabled>

      <div class="popup-btns">
        <button class="save-btn" id="saveRoom">Save</button>
        <button class="close-btn" id="closePopup">Close</button>
      </div>
    </div>
  </div>


  <footer>
    <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
  </footer>

</body>

</html>
