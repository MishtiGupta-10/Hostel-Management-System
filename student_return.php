<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "invalid";
    exit();
}

if (!isset($_POST["entry_id"])) {
    echo "missing";
    exit();
}

$entry_id = mysqli_real_escape_string($conn, $_POST["entry_id"]);

// Check if record is OUT (Exit_Time set, Entry_Time NULL)
$check = "SELECT 1 FROM Home_Entry
          WHERE Entry_Id = '$entry_id'
          AND Exit_Time IS NOT NULL
          AND Entry_Time IS NULL
          LIMIT 1";

$res = mysqli_query($conn, $check);

if (!$res) {
    echo "SQL Error: " . mysqli_error($conn);
    exit();
}

if (mysqli_num_rows($res) == 0) {
    echo "invalid_state";
    exit();
}

// Update return time
$update = "UPDATE Home_Entry
           SET Entry_Time = NOW(), status = 0
           WHERE Entry_Id = '$entry_id'";

if (mysqli_query($conn, $update)) {
    echo "success";
} else {
    echo "SQL Error: " . mysqli_error($conn);
}
?>
