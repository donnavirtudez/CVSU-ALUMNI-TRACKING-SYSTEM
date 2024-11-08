<?php
include_once("../connection/config.php");

session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../index.php");
    exit();
}

$email = $_SESSION['email'];

$stmt = $con->prepare("SELECT studentid FROM alumni WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($studentID);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $oldPassword = $_POST['password_old'];
    $newPassword = $_POST['password_new'];
    $confirmPassword = $_POST['password_confirm'];

    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('New password and confirm password do not match.');</script>";
    } else {
        $stmt = $con->prepare("SELECT password FROM account WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        $stmt->close();

        if ($oldPassword === $hashedPassword) {
            $update_stmt_account = $con->prepare("UPDATE account SET password = ? WHERE email = ?");
            $update_stmt_alumni = $con->prepare("UPDATE alumni SET password = ? WHERE email = ?");

            $update_stmt_account->bind_param("ss", $newPassword, $email);
            $update_stmt_alumni->bind_param("ss", $newPassword, $email);

            $success_account = $update_stmt_account->execute();
            $success_alumni = $update_stmt_alumni->execute();

            $update_stmt_account->close();
            $update_stmt_alumni->close();

            if ($success_account && $success_alumni) {
                echo "<script>alert('Password changed successfully!'); window.location.href='../php/alumniprofile.php'</script>";
            } else {
                echo "<script>alert('Error updating password in one of the tables.');</script>";
            }
        } else {
            echo "<script>alert('Old password is incorrect.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="icon" type="image/png" href="../pics/CVSU.png" />
    <link rel="stylesheet" href="../css/alumniprofile.css">
    <script>
        function confirmChange() {
            return confirm("Are you sure you want to change your password?");
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>Change Password</h2>
        <p><strong>Student ID:</strong> <?php echo htmlspecialchars($studentID); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <form method="POST" onsubmit="return confirmChange();">
            <input type="password" name="password_old" placeholder="Old Password" required>
            <input type="password" name="password_new" placeholder="New Password" required>
            <input type="password" name="password_confirm" placeholder="Confirm Password" required>
            <br>
            <button type="submit" name="change_password" class="button">Change</button>
            <button type="button" class="button" onclick="window.location.href='../php/alumniprofile.php';">Back</button>
        </form>
    </div>
</body>

</html>