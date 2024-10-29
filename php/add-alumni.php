<?php
include_once("../connection/config.php");

session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentID = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $graduationYear = $_POST['gradyear'];
    $program = $_POST['program'];
    $phone = $_POST['phone'];
    $employmentStatus = $_POST['employment_status'];

    $stmt = $con->prepare("INSERT INTO alumni (studentid, firstname, lastname, email, password, gradyear, program, phone, empstatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssisss", $studentID, $firstname, $lastname, $email, $password, $graduationYear, $program, $phone, $employmentStatus);

    if ($stmt->execute()) {
        $stmtAccount = $con->prepare("INSERT INTO account (email, password, role) VALUES (?, ?, ?)");
        $role = 'alumni';
        $stmtAccount->bind_param("sss", $email, $password, $role);

        if ($stmtAccount->execute()) {
            echo "<script>alert('Alumni added successfully!'); window.location.href='../php/dashboard.php';</script>";
        } else {
            echo "<script>alert('Error adding account: " . $stmtAccount->error . "'); window.location.href='../php/add-alumni.php';</script>";
        }

        $stmtAccount->close();
    } else {
        echo "<script>alert('Error adding alumni: " . $stmt->error . "'); window.location.href='../php/add-alumni.php';</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALUMNI TRACKING SYSTEM - ADD ALUMNI</title>
    <link rel="icon" type="image/png" href="../pics/CVSU.png" />
    <link rel="stylesheet" href="../css/add-alumni.css">
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
                <h2 id="alumni">Add Alumni</h2>
                <form id="alumniForm" method="POST" action="../php/add-alumni.php" onsubmit="return confirmAdd();">
                    <input type="text" name="id" placeholder="Student ID" required>
                    <input type="text" name="firstname" placeholder="Firstname" required>
                    <input type="text" name="lastname" placeholder="Lastname" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="text" name="gradyear" placeholder="Graduation Year" required>
                    <select name="program" required>
                        <option value="" selected disabled>Program</option>
                        <option value="Computer Science">Bachelor of Science in Computer Science</option>
                        <option value="Information Technology">Bachelor of Science in Information Technology</option>
                        <option value="Psychology">Bachelor of Science in Psychology</option>
                        <option value="Hospitality Management">Bachelor of Science in Hospitality Management</option>
                        <option value="Education - English">Bachelor of Science in Education - English</option>
                        <option value="Education - Math">Bachelor of Science in Education - Math</option>
                        <option value="Criminology">Bachelor of Science in Criminology</option>
                        <option value="Business Management">Bachelor of Science in Business Management</option>
                    </select>
                    <input type="tel" name="phone" placeholder="Phone Number">
                    <select name="employment_status" required>
                        <option value="" selected disabled>Employment Status</option>
                        <option value="Employed">Employed</option>
                        <option value="Unemployed">Unemployed</option>
                        <option value="Self-Employed">Self-Employed</option>
                        <option value="Student">Student</option>
                    </select>
                    <button type="submit">Add</button>
                </form>
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
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = '../php/logout.php';
            }
        }

        function confirmAdd() {
            return confirm("Are you sure you want to add this alumni?");
        }
    </script>
</body>

</html>