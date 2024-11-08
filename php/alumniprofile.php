<?php
include_once("../connection/config.php");

session_start();

$studentID = "";
$firstname = "";
$lastname = "";
$graduationYear = "";
$program = "";
$employmentStatus = "";
$email = "";
$phone = "";

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $con->prepare("SELECT studentid, firstname, lastname, gradyear, program, empstatus, phone FROM alumni WHERE email = ?");
    $stmt->bind_param("s", $email);

    $stmt->execute();
    $stmt->bind_result($studentID, $firstname, $lastname, $graduationYear, $program, $employmentStatus, $phone);
    $stmt->fetch();
    $stmt->close();
} else {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $graduationYear = $_POST['graduation-year'];
    $program = $_POST['program'];
    $employmentStatus = $_POST['employment_status'];
    $phone = $_POST['phone'];

    $update_stmt = $con->prepare("UPDATE alumni SET firstname = ?, lastname = ?, gradyear = ?, program = ?, empstatus = ?, phone = ? WHERE email = ?");
    $update_stmt->bind_param("sssssss", $firstname, $lastname, $graduationYear, $program, $employmentStatus, $phone, $email);

    if ($update_stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='';</script>";
    } else {
        echo "<script>alert('Error updating profile: " . $update_stmt->error . "'); window.location.href='';</script>";
    }
    $update_stmt->close();
}

if (isset($_GET['logout'])) {
    $_SESSION = [];

    session_destroy();

    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALUMNI TRACKING SYSTEM - ALUMNI PROFILE</title>
    <link rel="icon" type="image/png" href="../pics/CVSU.png" />
    <link rel="stylesheet" href="../css/alumniprofile.css">
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = '../php/alumniprofile.php?logout=true';
            }
        }

        function confirmSave() {
            return confirm("Are you sure you want to save changes?");
        }

        function redirectToChangePassword() {
            window.location.href = '../php/changepassword.php';
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="profile-header">
            <img src="../pics/profile.png" alt="Profile Picture" class="profile-picture">
            <p><span class="alumni-studID">Student ID:</span> <?php echo htmlspecialchars($studentID); ?></p>
        </div>

        <div class="profile-content">
            <h2>Alumni Details</h2>
            <form class="profile-form" method="POST" onsubmit="return confirmSave();">
                <input type="text" name="firstname" placeholder="Firstname" value="<?php echo htmlspecialchars($firstname); ?>" required>
                <input type="text" name="lastname" placeholder="Lastname" value="<?php echo htmlspecialchars($lastname); ?>" required>
                <input type="text" name="graduation-year" placeholder="Graduation Year" value="<?php echo htmlspecialchars($graduationYear); ?>" required>
                <select name="program" required>
                    <option value="<?php echo htmlspecialchars($program); ?>" selected><?php echo htmlspecialchars($program); ?></option>
                    <option value="Computer Science">Bachelor of Science in Computer Science</option>
                    <option value="Information Technology">Bachelor of Science in Information Technology</option>
                    <option value="Psychology">Bachelor of Science in Psychology</option>
                    <option value="Hospitality Management">Bachelor of Science in Hospitality Management</option>
                    <option value="Education - English">Bachelor of Science in Education - English</option>
                    <option value="Education - Math">Bachelor of Science in Education - Math</option>
                    <option value="Criminology">Bachelor of Science in Criminology</option>
                    <option value="Business Management">Bachelor of Science in Business Management</option>
                </select>
                <select name="employment_status" required>
                    <option value="<?php echo htmlspecialchars($employmentStatus); ?>" selected><?php echo htmlspecialchars($employmentStatus); ?></option>
                    <option value="Employed">Employed</option>
                    <option value="Unemployed">Unemployed</option>
                    <option value="Self-Employed">Self-Employed</option>
                    <option value="Student">Student</option>
                </select>

                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required readonly>
                <input type="tel" name="phone" placeholder="Phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                <button type="submit" class="button">Save Changes</button>
                <br>
                <button type="button" class="button" onclick="redirectToChangePassword();">Change Password</button> <!-- Change this button to redirect -->
                <br>
                <button type="button" class="button" onclick="confirmLogout();">Log out</button>
            </form>
        </div>
    </div>
</body>

</html>