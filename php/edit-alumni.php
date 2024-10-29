<?php
include_once("../connection/config.php");

session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../index.php");
    exit();
}

$studentID = isset($_GET['studentID']) ? $_GET['studentID'] : null;

if ($studentID) {
    $stmt = $con->prepare("SELECT firstname, lastname, email, gradyear, program, phone, empstatus FROM alumni WHERE studentid = ?");
    $stmt->bind_param("i", $studentID);
    $stmt->execute();
    $stmt->bind_result($firstname, $lastname, $email, $graduationYear, $program, $phone, $employmentStatus);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "<script>alert('Invalid Student ID.'); window.location.href='../php/dashboard.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $gradyear = $_POST['gradyear'];
    $program = $_POST['program'];
    $phone = $_POST['phone'];
    $employment_status = $_POST['employment_status'];

    $updateStmt = $con->prepare("UPDATE alumni SET firstname = ?, lastname = ?, email = ?, gradyear = ?, program = ?, phone = ?, empstatus = ? WHERE studentid = ?");
    $updateStmt->bind_param("sssssssi", $firstname, $lastname, $email, $gradyear, $program, $phone, $employment_status, $id);

    if ($updateStmt->execute()) {
        echo "<script>alert('Alumni information updated successfully.'); window.location.href='../php/dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating alumni information.'); window.location.href='../php/edit-alumni.php?studentID=" . htmlspecialchars($id) . "';</script>";
    }

    $updateStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALUMNI TRACKING SYSTEM - EDIT ALUMNI</title>
    <link rel="icon" type="image/png" href="../pics/CVSU.png" />
    <link rel="stylesheet" href="../css/edit-alumni.css">
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
                <li><a href="#" onclick="confirmLogout()">Log out</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <header>
                <h1>CAVITE STATE UNIVERSITY BACOOR CAMPUS</h1>
            </header>
            <section id="add">
                <h2 id="alumni">Edit Alumni</h2>
                <form id="editForm" action="../php/edit-alumni.php?studentID=<?php echo htmlspecialchars($studentID); ?>" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($studentID); ?>">
                    <input type="text" name="firstname" placeholder="Firstname" value="<?php echo htmlspecialchars($firstname); ?>" required>
                    <input type="text" name="lastname" placeholder="Lastname" value="<?php echo htmlspecialchars($lastname); ?>" required>
                    <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required readonly>
                    <input type="text" name="gradyear" placeholder="Graduation Year" value="<?php echo htmlspecialchars($graduationYear); ?>" required>
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
                    <input type="tel" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($phone); ?>">
                    <select name="employment_status" required>
                        <option value="<?php echo htmlspecialchars($employmentStatus); ?>" selected><?php echo htmlspecialchars($employmentStatus); ?></option>
                        <option value="Employed">Employed</option>
                        <option value="Unemployed">Unemployed</option>
                        <option value="Self-Employed">Self-Employed</option>
                        <option value="Student">Student</option>
                    </select>
                    <button type="button" onclick="confirmSave()">Save</button>
                    <br>
                    <button type="button" class="back-button" onclick="window.location.href='../php/dashboard.php';">Back</button>
                </form>
            </section>
        </main>
    </div>

    <script>
        function confirmSave() {
            if (confirm("Are you sure you want to save these changes?")) {
                document.getElementById('editForm').submit();
            }
        }

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = '../php/logout.php';
            }
        }

        const toggleButton = document.querySelector('.toggle-sidebar');
        const sidebar = document.querySelector('.sidebar');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    </script>
</body>

</html>