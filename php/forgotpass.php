<?php
include_once("../connection/config.php");

session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        if (isset($con)) {
            $stmt = $con->prepare("SELECT email FROM account WHERE email = ?");
            $stmt->bind_param("s", $email);

            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close();

                $update_stmt = $con->prepare("UPDATE account SET password = ? WHERE email = ?");
                $update_stmt->bind_param("ss", $new_password, $email);

                if ($update_stmt->execute()) {
                    $update_stmt_alumni = $con->prepare("UPDATE alumni SET password = ? WHERE email = ?");
                    $update_stmt_alumni->bind_param("ss", $new_password, $email);

                    if ($update_stmt_alumni->execute()) {
                        $message = "Password reset successful! You can now log in.";
                        echo "<script>alert('$message'); window.location.href='../index.php';</script>";
                        exit();
                    } else {
                        $message = "Error updating password in alumni table: " . $update_stmt_alumni->error;
                    }

                    $update_stmt_alumni->close();
                } else {
                    $message = "Error updating password in account table: " . $update_stmt->error;
                }
                $update_stmt->close();
            } else {
                $message = "Email not found.";
            }

            $stmt->close();
        } else {
            $message = "Database connection failed.";
        }
    }

    if (!empty($message)) {
        echo "<script>alert('$message');</script>";
    }
}

if (isset($con)) {
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALUMNI TRACKING SYSTEM - FORGOT PASSWORD</title>
    <link rel="icon" type="image/png" href="../pics/CVSU.png" />
    <link rel="stylesheet" href="../css/forgotpass.css">
</head>

<body>
    <div class="container">
        <div class="left-div">
            <img src="../pics/CVSU.png" alt="CVSU" class="logo">
            <h1 class="schoolname">CAVITE STATE UNIVERSITY BACOOR CAMPUS</h1>
        </div>
        <div class="right-div">
            <form method="POST">
                <h1 class="alumni">ALUMNI TRACKING SYSTEM</h1>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Reset Password</button>
                <br>
                <br>
                <a class="link" href="../index.php">Back</a>
            </form>
        </div>
    </div>
</body>

</html>