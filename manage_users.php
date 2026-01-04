<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include("connect.php");


if (isset($_POST['addWardenBtn'])) {

    $name = trim($_POST['Wname']);
    $gender = trim($_POST['Wgender']);
    $contact = trim($_POST['Wcontact']);
    $email = trim($_POST['Wemail']);

    if ($name === "" || $email === "" || $contact === "") {
        $flash = "Please fill all Warden fields.";
    } else {

        $sql = "INSERT INTO warden (Name, contact_info, Gender, Email)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $name, $contact, $gender, $email);

        if (mysqli_stmt_execute($stmt)) {

            $newWardenId = mysqli_insert_id($conn);
            $username = $newWardenId;
            $defaultPassword = "warden123";
            $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

            $update = "UPDATE warden 
                       SET username = '$username', password = '$hashedPassword'
                       WHERE Warden_Id = '$newWardenId'";

            mysqli_query($conn, $update);

            header("Location: manage_users.php?warden_added=1");
            exit;
        } 
        else {
            $flash = "Error Adding Warden: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Users</title>
<link rel="stylesheet" href="css/manage_users.css">
<style>
.container{max-width:1200px;margin:auto;padding:20px;}
form{background:white;padding:15px;border-radius:6px;margin-bottom:20px;}
input,select{width:100%;padding:8px;margin:5px 0;}
.btn{padding:8px 12px;background:#2563eb;color:white;border:none;border-radius:5px;cursor:pointer;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{padding:10px;border:1px solid #ddd;text-align:left;}
.flash{background:#fff3cd;padding:10px;border-radius:5px;margin-bottom:10px;}
</style>
</head>
<body>

<div class="container">
<h1>Manage Users</h1>

<?php if(!empty($_GET['warden_added'])): ?>
<div class="flash">Warden added successfully!</div>
<?php endif; ?>

<?php if(!empty($flash)): ?>
<div class="flash"><?= $flash ?></div>
<?php endif; ?>

<!-- --------------------------------------- -->
<!-- WARDEN FORM ONLY -->
<!-- --------------------------------------- -->
<h2>Add Warden</h2>
<form method="POST" class="user-form">

    <label>Name*</label>
    <input type="text" name="Wname" required>

    <label>Gender</label>
    <select name="Wgender">
        <option>Male</option>
        <option>Female</option>
    </select>

    <label>Contact*</label>
    <input type="text" name="Wcontact" maxlength="10" required>

    <label>Email*</label>
    <input type="email" name="Wemail" required>

    <button class="btn" name="addWardenBtn">Add Warden</button>
</form>



<!-- WARDEN TABLE -->
<h2>All Wardens</h2>

<table>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Gender</th>
    <th>Contact</th>
</tr>

<?php
$warden_q = "SELECT Warden_Id, Name, Email, Gender, contact_info FROM warden ORDER BY Name";
$warden_res = mysqli_query($conn, $warden_q);

if ($warden_res && mysqli_num_rows($warden_res) > 0) {
    while($r = mysqli_fetch_assoc($warden_res)){
        echo "<tr>";
        echo "<td>{$r['Name']}</td>";
        echo "<td>{$r['Email']}</td>";
        echo "<td>{$r['Gender']}</td>";
        echo "<td>{$r['contact_info']}</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No wardens found.</td></tr>";
}
?>
</table>

<!-- --------------------------------------- -->
<!-- STUDENT TABLE (DISPLAY ONLY) -->
<!-- --------------------------------------- -->

<h2>All Students</h2>

<table>
<tr>
    <th>Name</th>
    <th>Gender</th>
    <th>Contact</th>
    <th>Bdate</th>
    <th>Email</th>
</tr>

<?php
$student_q = "SELECT Student_Id, Fname, Lname, gender, contact_info, Bdate, Email
              FROM student ORDER BY Fname
";

$student_res = mysqli_query($conn, $student_q);

if ($student_res && mysqli_num_rows($student_res) > 0) {
    while($s = mysqli_fetch_assoc($student_res)){
        echo "<tr>";
        echo "<td>{$s['Fname']} {$s['Lname']}</td>";
        echo "<td>{$s['gender']}</td>";
        echo "<td>{$s['contact_info']}</td>";
        echo "<td>{$s['Bdate']}</td>";
        echo "<td>{$s['Email']}</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No students found.</td></tr>";
}
?>
</table>

</div>
</body>
</html>
