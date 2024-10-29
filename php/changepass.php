<?php
include_once("../connection/config.php");

session_start();

$email = "";
$oldPassword = "";
$newPassword = "";
$confirmPassword = "";
$message = "";

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
} else {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldPassword = $_POST['password_old'];
    $newPassword = $_POST['password_new'];
    $confirmPassword = $_POST['password_confirm'];

    if ($newPassword !== $confirmPassword) {
        $message = "New password and confirm password do not match.";
    } else {
        $stmt = $con->prepare("SELECT password FROM account WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($currentPassword);
        $stmt->fetch();
        $stmt->close();

        if ($oldPassword === $currentPassword) {
            $update_stmt = $con->prepare("UPDATE account SET password = ? WHERE email = ?");
            $update_stmt->bind_param("ss", $newPassword, $email);

            if ($update_stmt->execute()) {
                echo "<script>alert('Password changed successfully!'); window.location.href='../php/dashboard.php';</script>";
                exit();
            } else {
                $message = "Error updating password: " . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            $message = "Old password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALUMNI TRACKING SYSTEM - ADMIN CHANGE PASSWORD</title>
    <link rel="icon" type="image/png" href="../pics/CVSU.png" />
    <link rel="stylesheet" href="../css/changepass.css">
</head>

<body>
    <div class="container">
        <button class="toggle-sidebar">☰</button>
        <nav class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="../php/dashboard.php">View Alumni</a></li>
                <li><a href="../php/add-alumni.php">Add Alumni</a></li>
                <li><a href="../php/changepass.php">Change Password</a></li>
                <li><a href="../php/logout.php" onclick="return confirmLogout();">Log out</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <header>
                <h1>CAVITE STATE UNIVERSITY BACOOR CAMPUS</h1>
            </header>
            <section id="add">
                <h2 id="alumni">Change Password</h2>
                <form method="POST" onsubmit="return confirmChange();">
                    <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required readonly>
                    <input type="password" name="password_old" placeholder="Old Password" required>
                    <input type="password" name="password_new" placeholder="New Password" required>
                    <input type="password" name="password_confirm" placeholder="Confirm Password" required>
                    <button type="submit">Change</button>
                </form>
                <?php if ($message): ?>
                    <script>
                        alert("<?php echo htmlspecialchars($message); ?>");
                    </script>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        const toggleButton = document.querySelector('.toggle-sidebar');
        const sidebar = document.querySelector('.sidebar');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }

        function confirmChange() {
            return confirm("Are you sure you want to change your password?");
        }
    </script>
</body>

</html>