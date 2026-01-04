<?php
session_start();
include("connect.php");   // make sure $conn is your mysqli connection

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: index.html'); // or login page
    exit();
}

$role = $_POST['role'] ?? '';
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($role) || empty($username) || empty($password)) {
    echo "<script>alert('Please fill all fields'); history.back();</script>";
    exit();
}

// Map role -> table/columns/redirect
$map = [
    'admin' => [
        'table' => 'admin',
        'usercol' => 'username',
        'passcol' => 'password',     
        'idcol'   => 'Admin_Id',    
        'redirect' => 'admin_dashboard.php'
    ],
    'student' => [
        'table' => 'student',
        'usercol' => 'Email',
        'passcol' => 'password',
        'idcol'   => 'Student_Id',
        'redirect' => 'student_dashboard.php'
    ],
    'warden' => [
        'table' => 'warden',
        'usercol' => 'username',
        'passcol' => 'password',
        'idcol'   => 'Warden_Id',
        'redirect' => 'warden_dashboard.php'
    ]
];

if (!isset($map[$role])) {
    die("Invalid role selected.");
}

$conf = $map[$role];
$table = $conf['table'];
$usercol = $conf['usercol'];
$passcol = $conf['passcol'];
$idcol = $conf['idcol'];
$redirectPage = $conf['redirect'];

// Prepared statement to prevent SQL injection
$sql = "SELECT * FROM `$table` WHERE `$usercol` = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    error_log("Prepare failed: " . mysqli_error($conn));
    echo "<script>alert('Server error.'); history.back();</script>";
    exit();
}
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if ($res && mysqli_num_rows($res) === 1) {
    $row = mysqli_fetch_assoc($res);

    // Ensure passcol exists in result
    if (!array_key_exists($passcol, $row)) {
        echo "<script>alert('Server configuration error (password column).'); history.back();</script>";
        exit();
    }

    $hashed_pass = $row[$passcol];

    if (password_verify($password, $hashed_pass)) {
        // Credentials valid
        $_SESSION['role'] = $role;
        // store numeric id if available, otherwise store username
        if (isset($row[$idcol])) {
            $_SESSION['user_id'] = $row[$idcol];
        } else {
            $_SESSION['user_id'] = $row[$usercol];
        }

        // optional: regenerate session id
        session_regenerate_id(true);

        header("Location: $redirectPage");
        exit();
    } else {
        echo "<script>alert('Incorrect password'); history.back();</script>";
        exit();
    }
} else {
    echo "<script>alert('User not found'); history.back();</script>";
    exit();
}
?>
