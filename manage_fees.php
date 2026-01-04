<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Fees | HMS</title>
  <link rel="stylesheet" href="css/manage_fees.css" />
  
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <nav class="navbar">
      <div class="logo">HMS</div>
      <ul class="nav-links">
        <!-- <li><a href="templates/index.html">Home</a></li> -->
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="templates/logout.html" class="btn">Logout</a></li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <h2>Fee Records Overview</h2>

    <!-- Search & Filter -->
    <div class="filter-section">
        <form method = "GET" class = "filter-section">
            <input type="text" name = "search"  value = "<?= isset($_GET['search'])? $_GET['search']: ''?>" id="searchBox" placeholder="Search by student name..." />
            
            <select id="filterStatus" name = "status">
              <option value="All">All Status</option>
              <option value="Paid"<?= (isset($_GET['status']) && $_GET['status'] == "Paid") ? 'selected' : ''?>>Paid</option>
              <option value="Pending"<?= (isset($_GET['status']) && $_GET['status'] == "Pending")? 'selected' : ''?>>Pending</option>
            </select>

            <button type = "submit" class = "btn small-btn">Search</button>
        </form>
    </div>

    <?php
    include("connect.php");

    $search = isset($_GET['search']) ? $_GET['search'] : "";
    $status = isset($_GET['status']) ? $_GET['status'] : "All";

    $fees = "SELECT f.Fee_Id, f.Amount, f.Status, concat(Fname, ' ', Lname) as Name, h.Name as Hname
            FROM fee AS f
            JOIN student AS s ON f.Student_Id = s.Student_Id
            JOIN hostel AS h ON s.Hostel_Id = h.Hostel_Id
            WHERE 1";

    //search filter
    if (!empty($search)) 
    {
        $fees .= " AND CONCAT(s.Fname, ' ', s.Lname) LIKE '%$search%'";
    }
    
    //status filter
    if($status == "Paid")
    {
        $fees .= " AND f.Status = 1";
    }
    elseif ($status == "Pending")
    {
        $fees .= " AND f.Status = 0";
    }

    $result = mysqli_query($conn, $fees);
    ?>

    <!-- Fee Table -->
    <table id="feeTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Hostel</th>
          <th>Amount (₹)</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['Fee_Id']) ?></td>
                <td><?= htmlspecialchars($row['Name']) ?></td>
                <td><?= htmlspecialchars($row['Hname']) ?></td>
                <td><?= htmlspecialchars($row['Amount']) ?></td>
                <td style="color: <?php echo ($row['Status'] == 1) ? 'green' : 'red'; ?>;">
                    <?php echo($row['Status'] == 1) ? 'Paid' : 'Pending' ?>
                </td>
                <td>
                    <?php if($row['Status'] == 1) {?>
                        <form method = "post" action = "update_fees.php">
                            <input type = "hidden" name = "id" value = "<?= $row['Fee_Id']?>">
                            <button type = "submit" name = "Mark_Pending" class = "btn small-btn">Mark Pending</button>
                        </form>
                    <?php } ?>
                    <?php if($row['Status'] == 0) {?>
                        <form method = "post" action = "update_fees.php">
                            <input type = "hidden" name = "id" value = "<?= $row['Fee_Id']?>">
                            <button type = "submit" name = "Mark_Paid" class = "btn small-btn">Mark Paid</button>
                        </form>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
      </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary">
      <h3>Total Collection (₹): <span id="totalAmount">
      <?php
      $query3 = "select sum(Amount) as Total_Amount from fee where status = 1";
      $result = mysqli_query($conn, $query3);

      if($result)
      {
        $row = mysqli_fetch_assoc($result);
        echo htmlspecialchars($row['Total_Amount']);
      }
      ?>
      </span></h3>
    </div>

    <!-- Buttons -->
    <div class="button-section">
      <button class="btn" id="saveBtn">Save Updates</button>
    </div>
  </div>

  <footer>
        <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
  </footer>


  <script>
   
</script>
</body>
</html>
