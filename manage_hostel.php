<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Hostels | HMS</title>
  <link rel="stylesheet" href="css/manage_hostels.css" />
  
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <nav class="navbar">
      <div class="logo">HMS</div>
      <ul class="nav-links">
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="templates/logout.html" class="btn">Logout</a></li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <h2>Hostel Records</h2>
    <?php if (isset($_GET['added'])) { ?>
    <script>alert('Hostel added successfully!');</script>
    <?php } ?>

    <!-- Add / Edit Form -->
    <div class="form-section">
        <form name = "HostelForm" method = "POST" action = "">
            <input type="text" id="hostelName" name = "hostelName" placeholder="Hostel Name" />
            <input type="text" id="hostelType" name = "hostelType" placeholder="Type (Boys/Girls)" />
            <input type="number" id="capacity" name = "capacity" placeholder="Capacity" />
            <input type="text" id="warden" name = "wardenId" placeholder="Warden Id" />
            <button class="btn" type = "submit" name = "addHostelBtn" id="addHostelBtn">Add / Update</button>
        </form>
    </div>

    <?php
    include ("connect.php");
        if(isset($_POST["addHostelBtn"]))
        {
            $hostelName = $_POST['hostelName'];
            $hostelType = $_POST['hostelType'];
            $capacity = $_POST['capacity'];
            $wardenId = $_POST['wardenId'];

            $query = "insert into Hostel (Name, Type, Capacity, Warden_Id) values ('$hostelName', '$hostelType' , '$capacity' , '$wardenId')";

           if(mysqli_query($conn,$query))
            {
                header("Location:manage_hostel.php");
                exit;

                
            }
            else 
            {
                echo "<script>alert('Error adding hostel: " . mysqli_error($conn)."');</script>";
            }
        }
    ?>
    <!-- Hostels Table -->
    <table id="hostelTable">
      <thead>
        <tr>
          <th>Hostel Name</th>
          <th>Type</th>
          <th>Capacity</th>
          <th>Warden</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php

        $query = "select * from Hostel";
        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result) > 0)
        {
          while($row = mysqli_fetch_assoc($result))
          {
            echo "<tr>";
            echo "<td>". $row['Name']."</td>";
            echo "<td>". $row['Type']. "</td>";
            echo "<td>". $row['Capacity']."</td>";
            echo "<td>". $row['Warden_Id']."</td>";
            echo "<td>
                    <button class = 'btn small-btn editBtn'
                      data-id = '{$row['Hostel_Id']}'
                      data-name = '{$row['Name']}'
                      data-type = '{$row['Type']}'
                      data-capacity = '{$row['Capacity']}'
                      data-warden = '{$row['Warden_Id']}'>
                      Edit 
                    </button>

                  </td>";
            echo "</tr>";
          }         
        }
        else 
        {
          echo "<tr><td colspan = '5' style = 'text-align: center;'>No Hostels found</td></tr>";
        }

      ?>

      </tbody>
    </table>

      <div id="editModal" style="display:none; position:fixed; top:0; left:0; 
  width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
  <div style="background:white; padding:20px; border-radius:8px; width:350px;">
    <h3>Edit Hostel</h3>
    <form id="editForm">
      <input type="hidden" id="editHostelId" name="hostelId">

      <label>Hostel Name:</label><br>
      <input type="text" id="editHostelName" name="name"><br><br>

      <label>Type:</label><br>
      <input type="text" id="editHostelType" name="type"><br><br>

      <label>Capacity:</label><br>
      <input type="number" id="editHostelCapacity" name="capacity"><br><br>

      <label>Warden ID:</label><br>
      <input type="number" id="editWardenId" name="wardenId"><br><br>

      <button type="submit">Save Changes</button>
      <button type="button" id="closeModal">Cancel</button>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.editBtn').forEach(button => {
  button.addEventListener('click', function() {
    document.getElementById('editHostelId').value = this.dataset.id;
    document.getElementById('editHostelName').value = this.dataset.name;
    document.getElementById('editHostelType').value = this.dataset.type;
    document.getElementById('editHostelCapacity').value = this.dataset.capacity;
    document.getElementById('editWardenId').value = this.dataset.warden;

    document.getElementById('editModal').style.display = 'flex';
  });
});

document.getElementById('closeModal').addEventListener('click', () => {
  document.getElementById('editModal').style.display = 'none';
});

// AJAX submit form
document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch('update_hostel.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    alert(data);
    location.reload(); 
  });
});
</script>

<script>
// 🗑 Open Delete Modal and fill data
document.querySelectorAll('.deleteBtn').forEach(button => {
  button.addEventListener('click', function() {
    document.getElementById('deleteHostelId').value = this.dataset.id;
    document.getElementById('deleteHostelName').value = this.dataset.name;
    document.getElementById('deleteHostelType').value = this.dataset.type;
    document.getElementById('deleteHostelCapacity').value = this.dataset.capacity;
    document.getElementById('deleteWardenId').value = this.dataset.warden;

    document.getElementById('deleteModal').style.display = 'flex';
  });
});

// Close delete modal
document.getElementById('closeDeleteModal').addEventListener('click', () => {
  document.getElementById('deleteModal').style.display = 'none';
});

//  AJAX submit for delete
document.getElementById('deleteForm').addEventListener('submit', function(e) {
  e.preventDefault();
  if (confirm("Are you sure you want to delete this hostel?")) {
    const formData = new FormData(this);

    fetch('delete_hostel.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(data => {
      alert(data);
      location.reload();
    });
  }
});
</script>


    <div class="button-section">
      <button class="btn" id="saveBtn">Save Changes</button>
    </div>
  </div>

  <footer>
        <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
  </footer>

</body>
</html>
