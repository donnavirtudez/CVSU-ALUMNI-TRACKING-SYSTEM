<?php
include_once("../connection/config.php");

session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['deleteID'])) {
    $deleteID = $_GET['deleteID'];

    $emailStmt = $con->prepare("SELECT email FROM alumni WHERE studentid = ?");
    $emailStmt->bind_param("i", $deleteID);
    $emailStmt->execute();
    $emailStmt->bind_result($email);
    $emailStmt->fetch();
    $emailStmt->close();

    $deleteStmt = $con->prepare("DELETE FROM alumni WHERE studentid = ?");
    $deleteStmt->bind_param("i", $deleteID);

    if ($deleteStmt->execute()) {
        $deleteAccountStmt = $con->prepare("DELETE FROM account WHERE email = ?");
        $deleteAccountStmt->bind_param("s", $email);

        if ($deleteAccountStmt->execute()) {
            echo "<script>alert('Alumni and account information deleted successfully.'); window.location.href='../php/dashboard.php';</script>";
        } else {
            echo "<script>alert('Alumni deleted, but error deleting account information.'); window.location.href='../php/dashboard.php';</script>";
        }

        $deleteAccountStmt->close();
    } else {
        echo "<script>alert('Error deleting alumni information.'); window.location.href='../php/dashboard.php';</script>";
    }

    $deleteStmt->close();
    exit();
}

$alumniList = [];
$stmt = $con->prepare("SELECT studentid, firstname, lastname, email, gradyear, program, phone, empstatus FROM alumni");
$stmt->execute();
$stmt->bind_result($studentID, $firstname, $lastname, $email, $graduationYear, $program, $phone, $employmentStatus);

while ($stmt->fetch()) {
    $alumniList[] = [
        'studentID' => $studentID,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'graduationYear' => $graduationYear,
        'program' => $program,
        'phone' => $phone,
        'employmentStatus' => $employmentStatus,
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALUMNI TRACKING SYSTEM - ADMIN DASHBOARD</title>
    <link rel="icon" type="image/png" href="../pics/CVSU.png" />
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>
    <div class="container">
        <button class="toggle-sidebar">☰</button>
        <nav class="sidebar">
            <h2 class="dashboard">Dashboard</h2>
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
            <section id="view">
                <h2 id="alumni">Alumni List</h2>

                <input type="text" id="search" placeholder="Search" onkeyup="filterTable()">

                <div class="table-responsive">
                    <table id="alumniTable">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Graduation Year</th>
                                <th>Program</th>
                                <th>Phone Number</th>
                                <th>Employment Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alumniList as $alumni): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($alumni['studentID']); ?></td>
                                    <td><?php echo htmlspecialchars($alumni['firstname']); ?></td>
                                    <td><?php echo htmlspecialchars($alumni['lastname']); ?></td>
                                    <td><?php echo htmlspecialchars($alumni['email']); ?></td>
                                    <td><?php echo htmlspecialchars($alumni['graduationYear']); ?></td>
                                    <td><?php echo htmlspecialchars($alumni['program']); ?></td>
                                    <td><?php echo htmlspecialchars($alumni['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($alumni['employmentStatus']); ?></td>
                                    <td>
                                        <button class="btnEdit" type="button" onclick="confirmEdit('<?php echo htmlspecialchars($alumni['studentID']); ?>')">Edit</button>
                                        <br><br>
                                        <button class="btnDelete" type="button" onclick="confirmDelete('<?php echo htmlspecialchars($alumni['studentID']); ?>')">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        function filterTable() {
            const searchInput = document.getElementById('search').value.toLowerCase();
            const table = document.getElementById('alumniTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let rowVisible = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(searchInput)) {
                        rowVisible = true;
                        break;
                    }
                }

                rows[i].style.display = rowVisible ? '' : 'none';
            }
        }

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

        function confirmEdit(studentID) {
            window.location.href = `../php/edit-alumni.php?studentID=${studentID}`;
        }

        function confirmDelete(studentID) {
            if (confirm("Are you sure you want to delete this alumni?")) {
                window.location.href = `../php/dashboard.php?deleteID=${studentID}`;
            }
        }
    </script>
</body>

</html>