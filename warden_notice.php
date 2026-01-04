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
  <title>Warden Notice Management</title>
  <link rel="stylesheet" href="css/warden_notice.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">
</head>

<body>

  <!-- Header -->
  <header class="header">
    <div class="logo">HMS</div>
    <nav class="navbar">
      <a href="warden_dashboard.php">Home</a>
      <a href="student_records.php">Student Records</a>
      <a href="room_management.php">Room Management</a>
      <a href="complaint_management.php">Complaints</a>
      <a href="reports.php">Reports</a>
      <a href="outing.php">Outing Request</a>
      <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
  </header>

  <!-- MAIN SECTION -->
  <main class="container">
    <h2>Manage Notices</h2>

    <!-- Search + Add Notice Button -->
    <div class="top-bar">
      <div class="search-bar">
        <input id="searchInput" type="text" placeholder="Search by Notice ID, Title or Date...">
        <button id="searchBtn">Search</button>
        <button id="clearBtn" class="muted">Clear</button>
      </div>
      <button id="openAddBtn" class="add-btn">+ Add Notice</button>
    </div>

    <!-- Notices Table -->
    <div class="table-container">
      <table id="noticeTable">
        <thead>
          <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
        <?php
        $noticeQuery = "SELECT notice_id, title, description, date 
                        FROM Notice
                        WHERE hostel_id = '$hostel_id'
                        ORDER BY notice_id DESC";

        $result = mysqli_query($conn, $noticeQuery);

        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
        ?>
        <tr 
            data-id="<?= $row['notice_id'] ?>"
            data-title="<?= htmlspecialchars($row['title']) ?>"
            data-date="<?= $row['date'] ?>"
            data-content="<?= htmlspecialchars($row['description']) ?>"
        >
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= $row['date'] ?></td>

            <td class="actions">
                <button class="view-btn">View</button>
                <button class="edit-btn">Edit</button>
                <button class="remove-btn">Remove</button>
            </td>
        </tr>

        <?php
            }
        }
        ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- VIEW NOTICE MODAL -->
  <div id="viewModal" class="modal">
    <div class="modal-content">
      <button class="modal-close">&times;</button>
      <h3>Notice Details</h3>
      
      <div class="detail-row"><strong>Title:</strong> <span id="viewTitle"></span></div>
      <div class="detail-row"><strong>Date:</strong> <span id="viewDate"></span></div>
      <div class="detail-row full"><strong>Content:</strong> <p id="viewContent"></p></div>

      <div class="modal-actions">
        <button id="viewEditBtn" class="edit-btn">Edit</button>
        <button class="modal-close secondary">Close</button>
      </div>
    </div>
  </div>

  <!-- ADD / EDIT MODAL -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <button class="modal-close">&times;</button>
      <h3 id="modalTitle">Add Notice</h3>

      <form id="editForm">
        
        <label>Title
          <input id="editTitle" required />
        </label>


        <label>Content
          <textarea id="editContent" required></textarea>
        </label>

        <div class="modal-actions">
          <button type="submit" class="edit-btn">Save</button>
          <button type="button" class="modal-close secondary">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- JS -->
   <script>

    const $ = s => document.querySelector(s);
    const $$ = s => Array.from(document.querySelectorAll(s));

    const tbody = document.querySelector("#noticeTable tbody");

    function normalize(s) { return String(s || "").toLowerCase().trim(); }

    function runSearch() {
        const q = normalize($("#searchInput").value);
        const rows = [...tbody.rows];

        rows.forEach(row => {
            const content = `${row.dataset.id} ${row.dataset.title} ${row.dataset.date}`.toLowerCase();
            row.style.display = content.includes(q) ? "" : "none";
        });
    }

    $("#searchBtn").onclick = runSearch;
    $("#clearBtn").onclick = () => { $("#searchInput").value = ""; runSearch(); };


    function openModal(m) { m.style.display = "flex"; }
    function closeModal(m) { m.style.display = "none"; }

    $$(".modal-close").forEach(btn => {
        btn.addEventListener("click", () => closeModal(btn.closest(".modal")));
    });

    document.addEventListener("click", e => {
        if (e.target.classList.contains("modal")) closeModal(e.target);
    });

    const viewModal = $("#viewModal");
    const viewId = $("#viewId");
    const viewTitle = $("#viewTitle");
    const viewDate = $("#viewDate");
    const viewContent = $("#viewContent");

    tbody.addEventListener("click", e => {
        const btn = e.target;
        const row = btn.closest("tr");
        if (!row) return;

        /* VIEW */
        if (btn.classList.contains("view-btn")) {
            viewId.textContent = row.dataset.id;
            viewTitle.textContent = row.dataset.title;
            viewDate.textContent = row.dataset.date;
            viewContent.textContent = row.dataset.content;

            viewModal.currentRow = row;
            openModal(viewModal);
        }

        /* DELETE NOTICE (AJAX) */
        if (btn.classList.contains("remove-btn")) {
            if (!confirm(`Delete notice ${row.dataset.id}?`)) return;

            fetch("delete_notice.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "id=" + row.dataset.id
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") {
                    row.remove();
                    alert("Notice deleted!");
                } else {
                    alert("Delete failed.");
                }
            });
        }

        /* EDIT BUTTON */
        if (btn.classList.contains("edit-btn")) {
            openEdit(row);
        }
    });


    const editModal = $("#editModal");
    const modalTitle = $("#modalTitle");
    const editTitle = $("#editTitle");
    const editContent = $("#editContent");

    function openEdit(row) {
        modalTitle.textContent = "Edit Notice";
        editTitle.value = row.dataset.title;
        editContent.value = row.dataset.content;

        editModal.currentRow = row;
        openModal(editModal);
    }

    /* View → Edit button */
    $("#viewEditBtn").onclick = () => {
        closeModal(viewModal);
        openEdit(viewModal.currentRow);
    };


    $("#openAddBtn").onclick = () => {
        modalTitle.textContent = "Add Notice";
        editForm.reset();
        editModal.currentRow = null;
        openModal(editModal);
    };


    const editForm = $("#editForm");

    editForm.addEventListener("submit", e => {
        e.preventDefault();

        const title = editTitle.value.trim();
        const content = editContent.value.trim();


        if (!editModal.currentRow) {
            const fd = new FormData();
            fd.append("title", title);
            fd.append("description", content);

            fetch("add_notice.php", { method: "POST", body: fd })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") {
                    alert("Notice added!");
                    location.reload();
                } else {
                    alert("Error adding notice.");
                }
            });
        }


        else {
            const fd = new FormData();
            fd.append("title", title);
            fd.append("description", content);

            fetch("update_notice.php", { method: "POST", body: fd })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") {
                    alert("Notice updated!");
                    location.reload();
                } else {
                    alert("Error updating notice.");
                }
            });
        }

        closeModal(editModal);
    });
    </script>


  <footer>
    <p>© HMS Krrish Kumar, Pratham Maurya, Mishti Gupta Rights Reserved</p>
  </footer>

</body>

</html>
