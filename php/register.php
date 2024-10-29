<?php
include_once("../connection/config.php");

session_start();

$message = "";

function displayMessage($msg)
{
    return "<script>alert('$msg');</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmpass = $_POST['confirmpass'];
    $gradyear = $_POST['gradyear'];
    $program = $_POST['program'];
    $phone = $_POST['phone'];
    $employment_status = $_POST['employment_status'];

    if ($password !== $confirmpass) {
        $message = displayMessage("Passwords do not match.");
    } else {
        $stmt = $con->prepare("INSERT INTO alumni (studentid, firstname, lastname, email, password, gradyear, program, phone, empstatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssisis", $id, $firstname, $lastname, $email, $password, $gradyear, $program, $phone, $employment_status);

        if ($stmt->execute()) {
            $stmt_account = $con->prepare("INSERT INTO account (email, password, role) VALUES (?, ?, ?)");
            $role = 'alumni';
            $stmt_account->bind_param("sss", $email, $password, $role);

            if ($stmt_account->execute()) {
                $message = "<script>alert('Registration successful!'); window.location.href='../index.php';</script>";
            } else {
                $message = displayMessage("Error inserting into account: " . $stmt_account->error);
            }

            $stmt_account->close();
        } else {
            $message = displayMessage("Error: " . $stmt->error);
        }

        $stmt->close();
        $con->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALUMNI TRACKING SYSTEM - REGISTRATION</title>
    <link rel="icon" type="image/png" href="../pics/CVSU.png" />
    <link rel="stylesheet" href="../css/register.css">
    <script>
        function confirmRegistration() {
            return confirm("Are you sure you want to register with the provided information?");
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="left-div">
            <img src="../pics/CVSU.png" alt="CVSU" class="logo">
            <h1 class="schoolname">CAVITE STATE UNIVERSITY BACOOR CAMPUS</h1>
        </div>
        <div class="right-div">
            <form method="POST" onsubmit="return confirmRegistration();">
                <h1 class="alumni">ALUMNI TRACKING SYSTEM</h1>
                <?php
                if (!empty($message)) {
                    echo $message;
                }
                ?>
                <input type="text" name="id" placeholder="Student ID" required>
                <input type="text" name="firstname" placeholder="Firstname" required>
                <input type="text" name="lastname" placeholder="Lastname" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirmpass" placeholder="Confirm Password" required>
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
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <select name="employment_status" required>
                    <option value="" selected disabled>Employment Status</option>
                    <option value="Employed">Employed</option>
                    <option value="Unemployed">Unemployed</option>
                    <option value="Self-Employed">Self-Employed</option>
                    <option value="Student">Student</option>
                </select>
                <button type="submit">Register</button>
                <br>
                <br>
                <p class="p-a">Already have an account?<a href="../index.php"> Log In</a></p>
            </form>
        </div>
    </div>
</body>

</html>