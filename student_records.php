<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "warden") {
    header("Location: templates/login.html");
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
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Student Records | Hostel Management System</title>
  <link rel="stylesheet" href="css/student_records.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
</head>

<body>
  <!-- Header -->
  <header class="header">
    <div class="logo">HMS</div>
    <nav class="navbar">
      <a href="warden_dashboard.php">Home</a>
      <a href="room_management.php">Room Management</a>
      <a href="complaint_management.php">Complaints</a>
      <a href="reports.php">Reports</a>
      <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
  </header>

  <!-- Main -->
  <main class="container">
    <h2>Student Records</h2>

    <!-- Search Bar -->
    <div class="search-bar">
      <input id="searchInput" type="text" placeholder="Search by ID, Name or Room..." />
      <button id="searchBtn">Search</button>
      <button id="clearBtn" class="muted">Clear</button>
    </div>

    <!-- Table -->
    <div class="table-container">
      <table id="studentsTable" aria-describedby="student-records">
        <thead>
          <tr>
            <th>Name</th>
            <th>Room No</th>
            <th>Hostel</th>
            <th>Contact</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $student = "SELECT 
                      S.Student_Id, 
                      CONCAT(S.Fname, ' ', S.Lname) AS Student_Name, 
                      R.Room_No AS Room_No, 
                      H.Name AS Hostel_Name, 
                      contact_info
                      FROM Student S
                      INNER JOIN Room R
                      ON S.Room_Id = R.Room_Id
                      INNER JOIN Hostel H
                      ON S.Hostel_Id = H.Hostel_Id
                      where S.Hostel_Id = '$hostel_id' ";
            
            $result = mysqli_query($conn, $student);

            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
          ?>
        <tr
            data-id="<?= htmlspecialchars($row['Student_Id']) ?>"     
            data-name="<?= htmlspecialchars($row['Student_Name']) ?>"
            data-room="<?= htmlspecialchars($row['Room_No']) ?>"
            data-hostel="<?= htmlspecialchars($row['Hostel_Name']) ?>"
            data-contact="<?= htmlspecialchars($row['contact_info']) ?>"
        >
            <td><?= htmlspecialchars($row['Student_Name'])?></td>
            <td><?= htmlspecialchars($row['Room_No'])?></td>
            <td><?= htmlspecialchars($row['Hostel_Name'])?></td>
            <td><?= htmlspecialchars($row['contact_info'])?></td>
            <td>
                <div class = "actions">
                    <button class="edit-btn">Edit</button>
                    <button class="remove-btn">Remove</button>
                    <button class="view-btn">View Details</button>
                </div>
            </td>
        </tr>
        <?php }
        } ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- View Details Modal -->
  <div id="viewModal" class="modal" aria-hidden="true" role="dialog" aria-labelledby="viewModalTitle">
    <div class="modal-content">
      <button class="modal-close" aria-label="Close view modal">&times;</button>
      <h3 id="viewModalTitle">Student Details</h3>
      <div class="detail-row"><strong>ID:</strong> <span id="detailId"></span></div>
      <div class="detail-row"><strong>Name:</strong> <span id="detailName"></span></div>
      <div class="detail-row"><strong>Room:</strong> <span id="detailRoom"></span></div>
      <div class="detail-row"><strong>Hostel:</strong> <span id="detailHostel"></span></div>
      <div class="detail-row"><strong>Contact:</strong> <span id="detailContact"></span></div>
      <div class="modal-actions">
        <button id="viewEditBtn" class="edit-btn">Edit</button>
        <button class="modal-close secondary">Close</button>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="modal" aria-hidden="true" role="dialog" aria-labelledby="editModalTitle">
    <div class="modal-content">
      <button class="modal-close" aria-label="Close edit modal">&times;</button>
      <h3 id="editModalTitle">Edit Student</h3>
      <form id="editForm">
        <label>
          Student ID
          <input id="editId" name="id" readonly />
        </label>
        <label>
          Name
          <input id="editName" name="name" required />
        </label>
        <label>
          Room No
          <input id="editRoom" name="room" required />
        </label>
        <label>
          Hostel
          <input id="editHostel" name="hostel" required />
        </label>
        <label>
          Contact
          <input id="editContact" name="contact" required />
        </label>

        <div class="modal-actions">
          <button type="submit" class="edit-btn">Save</button>
          <button type="button" class="modal-close secondary">Cancel</button>
        </div>
      </form>
    </div>
  </div>
  <script>
  const $ = selector => document.querySelector(selector);
  const $$ = selector => Array.from(document.querySelectorAll(selector));

  const searchInput = $('#searchInput');
  const searchBtn = $('#searchBtn');
  const clearBtn = $('#clearBtn');
  const table = $('#studentsTable');
  const tbody = table.querySelector('tbody');

  function normalize(s) { return String(s || '').toLowerCase().trim(); }

  function runSearch() {
    const q = normalize(searchInput.value);
    const rows = [...tbody.rows];

    rows.forEach(row => {
      const combined = `${row.dataset.id} ${row.dataset.name} ${row.dataset.room}`.toLowerCase();
      row.style.display = combined.includes(q) ? '' : 'none';
    });
  }

  searchBtn.addEventListener('click', runSearch);
  clearBtn.addEventListener('click', () => {
    searchInput.value = '';
    runSearch();
  });
  searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      runSearch();
    }
  });

  /*Modal Helpers*/
  function openModal(modal) {
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
  }
  function closeModal(modal) {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
  }
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
      closeModal(e.target);
    }
  });
  $$('.modal .modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
      closeModal(btn.closest('.modal'));
    });
  });

  /*VIEW DETAILS*/
  const viewModal = $('#viewModal');
  const detailId = $('#detailId');
  const detailName = $('#detailName');
  const detailRoom = $('#detailRoom');
  const detailHostel = $('#detailHostel');
  const detailContact = $('#detailContact');
  const viewEditBtn = $('#viewEditBtn');

  function fillDetailsFromRow(row) {
    detailId.textContent = row.dataset.id;
    detailName.textContent = row.dataset.name;
    detailRoom.textContent = row.dataset.room;
    detailHostel.textContent = row.dataset.hostel;
    detailContact.textContent = row.dataset.contact;
  }

  /*EDIT MODAL*/
  const editModal = $('#editModal');
  const editForm = $('#editForm');
  const editId = $('#editId');
  const editName = $('#editName');
  const editRoom = $('#editRoom');
  const editHostel = $('#editHostel');
  const editContact = $('#editContact');

  function openEditModalForRow(row) {
    editId.value = row.dataset.id;
    editName.value = row.dataset.name;
    editRoom.value = row.dataset.room;
    editHostel.value = row.dataset.hostel;
    editContact.value = row.dataset.contact;

    editModal.currentRow = row;
    openModal(editModal);
  }

  /*TABLE BUTTON ACTIONS*/
  tbody.addEventListener('click', (e) => {
    const btn = e.target;
    const row = btn.closest('tr');
    if (!row) return;

    
    if (btn.classList.contains('view-btn')) {
      fillDetailsFromRow(row);
      viewModal.currentRow = row;
      openModal(viewModal);
    }

  
    if (btn.classList.contains('remove-btn')) {
      const sid = row.dataset.id;
      const ok = confirm(`Are you sure you want to delete Student ID ${sid}?`);
      if (!ok) return;

      fetch("delete_student.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "student_id=" + encodeURIComponent(sid)
      })
      .then(res => res.text())
      .then(data => {
        if (data.trim() === "success") {
          row.remove();
          alert("Student removed successfully!");
        } else {
          alert("Failed to delete.");
        }
      });
    }

  
    if (btn.classList.contains('edit-btn')) {
      openEditModalForRow(row);
    }
  });

  /*EDIT FORM SUBMISSION*/
  editForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const row = editModal.currentRow;
    if (!row) return;

    const formData = new FormData();
    formData.append("id", editId.value);
    formData.append("name", editName.value);
    formData.append("room", editRoom.value);
    formData.append("hostel", editHostel.value);
    formData.append("contact", editContact.value);

    fetch("update_student.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(data => {
      if (data.trim() === "success") {

        // Update UI
        row.dataset.name = editName.value;
        row.dataset.room = editRoom.value;
        row.dataset.hostel = editHostel.value;
        row.dataset.contact = editContact.value;

        const cells = row.cells;
        cells[0].textContent = editName.value;
        cells[1].textContent = editRoom.value;
        cells[2].textContent = editHostel.value;
        cells[3].textContent = editContact.value;

        closeModal(editModal);
        alert("Student updated successfully!");
      } else {
        alert("Update failed.");
      }
    });
  });

  /*VIEW → EDIT BUTTON*/
  viewEditBtn.addEventListener('click', () => {
    const row = viewModal.currentRow;
    if (row) {
      closeModal(viewModal);
      openEditModalForRow(row);
    }
  });

  /* Hide modals initially */
  $$('.modal').forEach(m => {
    m.style.display = 'none';
    m.setAttribute('aria-hidden', 'true');
  });
  </script>

  <footer>
    <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
  </footer>
</body>

</html>