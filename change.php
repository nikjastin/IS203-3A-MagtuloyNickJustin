<?php
session_start(); // Start the session

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
    $email = $_POST['email']; // Get the email from the form
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Fetch the current hashed password from the database
    $stmt = $conn->prepare("SELECT Password FROM tbtb WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Bind result
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the current password
        if (password_verify($currentPassword, $hashed_password)) {
            // Check if new password and confirm password match
            if ($newPassword === $confirmPassword) {
                // Hash the new password
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the password in the database
                $updateStmt = $conn->prepare("UPDATE tbtb SET Password = ? WHERE Email = ?");
                $updateStmt->bind_param("ss", $newHashedPassword, $email);

                if ($updateStmt->execute()) {
                    echo "<script>alert('Password changed successfully!');</script>";
                    echo "<script>window.location.href = 'login.php';</script>"; // Redirect to the login page
                } else {
                    echo "<script>alert('Error updating password.');</script>";
                }
                $updateStmt->close();
            } else {
                echo "<script>alert('New password and confirmation do not match.');</script>";
            }
        } else {
            echo "<script>alert('Current password is incorrect.');</script>";
        }
    } else {
        echo "<script>alert('No user found with this email.');</script>";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        /* Add your styles here, similar to your login.php */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #1f3c52;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 400px;
            padding: 40px;
            text-align: center;
        }

        h2 {
            color: #1877f2;
            margin-bottom: 20px;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        input[type="password"]:focus, input[type="text"]:focus {
            border-color: #1877f2;
            background-color: #fff;
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #1877f2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #165eab;
        }

        .back-button {
            background-color: #ddd;
            color: black;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 20px;
        }

        .back-button:hover {
            background-color: #ccc;
        }

        .back-button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <form action="change.php" method="POST">
            <label for="email">Email:</label>
            <input type="text" name="email" required placeholder="Enter your email">

            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" required placeholder="Enter your current password">

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" required placeholder="Enter your new password">

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" required placeholder="Confirm your new password">

            <input type="submit" value="Change Password">
        </form>
        <div class="back-button-container">
            <button type="button" class="back-button" onclick="window.location.href='login.php'">Back to Login</button>
        </div>
    </div>
</body>
</html>
