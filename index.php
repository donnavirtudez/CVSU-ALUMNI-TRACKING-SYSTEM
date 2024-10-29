<?php
include_once("connection/config.php");

session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (isset($con)) {
        $stmt = $con->prepare("SELECT password, role FROM account WHERE email = ?");
        $stmt->bind_param("s", $email);

        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($stored_password, $role);
            $stmt->fetch();

            if ($password === $stored_password) {
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;

                if ($role === 'admin') {
                    echo "<script>window.location.href = 'php/dashboard.php';</script>";
                    exit();
                } elseif ($role === 'alumni') {
                    echo "<script>window.location.href = 'php/alumniprofile.php';</script>";
                    exit();
                }
            } else {
                $message = "Invalid email or password.";
            }
        } else {
            $message = "Invalid email or password.";
        }

        $stmt->close();
    } else {
        $message = "Database connection failed.";
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
    <title>ALUMNI TRACKING SYSTEM - LANDING PAGE</title>
    <link rel="shortcut icon" type="x-icon" href="../pics/CVSU.png" />
    <link rel="stylesheet" href="index.css">
    <script>
        function showAlert(message) {
            alert(message);
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="left-div">
            <img src="pics/CVSU.png" alt="CVSU" class="logo">
            <h1 class="schoolname">CAVITE STATE UNIVERSITY BACOOR CAMPUS</h1>
        </div>
        <div class="right-div">
            <form method="POST" action="">
                <h1 class="alumni">ALUMNI TRACKING SYSTEM</h1>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Log In</button>
                <br><br>
                <a class="links" href="php/register.php">Register</a>
                <a class="links" href="php/forgotpass.php">Forgot Password</a>

                <?php
                if (!empty($message)) {
                    echo "<script>showAlert('$message');</script>";
                }
                ?>
            </form>
        </div>
    </div>
</body>

</html>