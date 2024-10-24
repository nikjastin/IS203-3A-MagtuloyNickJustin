<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbnj";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $gender = $_POST['gender'];
    $month = $_POST['month'];
    $day = $_POST['day'];
    $year = $_POST['year'];
    $account_type = $_POST['account_type'];  // Get account type from form

    // Validate name fields
    if (!preg_match("/^[a-zA-Z]+$/", $firstname)) {
        echo "<script>alert('Enter a valid First Name!');</script>";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $middlename)) {
        echo "<script>alert('Enter a valid Middle Name!');</script>";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $lastname)) {
        echo "<script>alert('Enter a valid Last Name!');</script>";
    } elseif ($password == $confirmpassword) {
        // Encrypt the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Set Admin and User values based on account type
        $is_admin = $account_type == 'admin' ? 1 : 0;
        $is_user = $account_type == 'user' ? 1 : 0;

        // Check if the email already exists
        $email_check = "SELECT * FROM tbregistration WHERE Email = '$email'";
        $result_email = $conn->query($email_check);

        // Check if the name combination (firstname, middlename, lastname) already exists
        $name_check = "SELECT * FROM tbregistration WHERE Firstname = '$firstname' AND Middlename = '$middlename' AND Lastname = '$lastname'";
        $result_name = $conn->query($name_check);

        if ($result_email->num_rows > 0) {
            // If the email already exists
            echo "<script>alert('Email is already registered!');</script>";
        } elseif ($result_name->num_rows > 0) {
            // If the name combination already exists
            echo "<script>alert('This name combination is already registered!');</script>";
        } else {
            // If no duplicates found, proceed with the insertion
            $sql = "INSERT INTO tbregistration (Firstname, Middlename, Lastname, Email, Password, Male, Female, Month, Day, Year, Admin, User) 
                    VALUES ('$firstname', '$middlename', '$lastname', '$email', '$hashed_password', 
                    '".($gender == 'male' ? 'male' : '')."', '".($gender == 'female' ? 'female' : '')."', 
                    '$month', '$day', '$year', '$is_admin', '$is_user')";

            if ($conn->query($sql) === TRUE) {
                // Show a successful registration message
                echo "<script>alert('Successfully Registered!');</script>";
                
                // Redirect to the appropriate page based on account type
                if ($is_admin) {
                    // Redirect to admin_login.php for admin
                    echo "<script>window.location.href = 'admin_login.php';</script>";
                } else {
                    // Redirect to login.php for user
                    echo "<script>window.location.href = 'login.php';</script>";
                }
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    } else {
        echo "<script>alert('Passwords do not match!');</script>"; // Show an alert if passwords do not match
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account</title>
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #1f3c52; /* Dark blue background */
}
.container {
    width: 100%;
    max-width: 400px;
    background-color: #2d485f; /* Darker section for the form */
    border-radius: 10px;
    padding: 40px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
}
h2 {
    color: #00d9b7; /* Teal color */
    text-align: center;
    margin-bottom: 30px;
    font-size: 26px;
}
label {
    color: #fff;
    display: block;
    margin-bottom: 8px;
}
input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f8f9fa;
    font-size: 14px;
}
input[type="submit"] {
    background-color: #00d9b7; /* Teal button */
    color: white;
    padding: 12px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
}
input[type="submit"]:hover {
    background-color: #00bfa5;
}
a {
    color: #00d9b7;
    display: block;
    text-align: center;
    margin-top: 20px;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
.gender-container {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}
.gender-container label {
    font-weight: 400;
}
.birthday-container {
    display: flex;
    justify-content: space-between;
}
.birthday-container select {
    width: 30%;
    padding: 10px;
    margin-bottom: 20px;
    background-color: #f8f9fa;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.footer {
    text-align: center;
    color: white;
    margin-top: 20px;
    font-size: 12px;
}
        
.show-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px; /* Adjust size as needed */
        }

        .password-container {
            position: relative;
        }
    </style>
    <script>
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById(inputId + "_eye");
            if (input.type === "password") {
                input.type = "text";
                eyeIcon.innerHTML = "&#128065;"; // Eye open icon (Unicode)
            } else {
                input.type = "password";
                eyeIcon.innerHTML = "&#128586;"; // Eye closed icon (Unicode)
            }
        }
    </script>
</head>
<body>
    <div class="container">
    <h2><a href="admin_login.php">Create an Account</a></h2>
        <form action="registration.php" method="POST">
            <label for="firstname">First Name</label>
            <input type="text" name="firstname" required>

            <label for="middlename">Middle Name</label>
            <input type="text" name="middlename" required>

            <label for="lastname">Last Name</label>
            <input type="text" name="lastname" required>

            <label for="email">Email</label>
            <input type="email" name="email" required>

            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <span id="password_eye" class="show-password" onclick="togglePasswordVisibility('password')">&#128586;</span> <!-- Closed eye icon -->
            </div>

            <label for="confirmpassword">Confirm Password</label>
            <div class="password-container">
                <input type="password" id="confirmpassword" name="confirmpassword" required>
                <span id="confirmpassword_eye" class="show-password" onclick="togglePasswordVisibility('confirmpassword')">&#128586;</span> <!-- Closed eye icon -->
            </div>

            <label>Gender</label>
            <div class="gender-container">
                <label><input type="radio" name="gender" value="male" required> Male</label>
                <label><input type="radio" name="gender" value="female" required> Female</label>
            </div>

            <label>Account Type</label>
            <div class="account-type-container">
                <label><input type="radio" name="account_type" value="user" required> User</label>
                <label><input type="radio" name="account_type" value="admin" required> Admin</label>
            </div>

            <label>Birthday</label>
            <div class="birthday-container">
                <select name="month" required>
                    <option value="">Month</option>
                    <?php
                    for($i = 1; $i <= 12; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
                <select name="day" required>
                    <option value="">Day</option>
                    <?php
                    for($i = 1; $i <= 31; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
                <select name="year" required>
                    <option value="">Year</option>
                    <?php
                    $currentYear = date("Y");
                    for($i = 1900; $i <= $currentYear; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
            </div>

            <input type="submit" value="Create Account">
            <a href="login.php">Already have an account? Log in</a>
        </form>
        <div class="footer">
            <p>&copy; Programmed by Nick Justin Magtuloy.</p>
        </div>
    </div>
</body>
</html>