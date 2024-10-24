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

session_start();

// Display login message if set
if (isset($_SESSION['login_message'])) {
    echo "<script>alert('" . $_SESSION['login_message'] . "');</script>";
    unset($_SESSION['login_message']);
}

// Processing login when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM tbreg WHERE Email = ? AND Admin = 1"; // Admin = 1 means admin account
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // "s" stands for string type

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['Password'])) {
            // Set session variables to keep the user logged in
            $_SESSION['id'] = $row['id'];
            $_SESSION['email'] = $row['Email'];
            $_SESSION['firstname'] = $row['Firstname'];
            $_SESSION['lastname'] = $row['Lastname']; 
            $_SESSION['account_type'] = 'admin'; // Set account type to 'admin'

            // Set session message for successful login
            $_SESSION['login_message'] = "Successfully logged in!";

            // Redirect to create.php after successful login
            header("Location: create.php");
            exit();
        } else {
            // Incorrect password
            echo "<script>alert('Incorrect password!');</script>";
        }
    } else {
        // No admin account found
        echo "<script>alert('Admin not found! Try Again.');</script>";
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
        <title>Admin Login</title>
        <style>
            body, html {
                font-family: Arial, sans-serif;
                background-image: url('uploads/1111131.jpg'); /* Path to your image */
                background-size: cover; /* Cover the entire viewport */
                background-position: center; /* Center the image */
                background-repeat: no-repeat; /* Prevent repeating */
                padding: 20px;
            }
            
            body {
                font-family: 'Poppins', sans-serif;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #1f3c52;
            }
            .container {
                width: 100%;
                max-width: 400px;
                background-color: #2d485f;
                border-radius: 10px;
                padding: 40px;
                box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            }
            h2 {
                color: #00d9b7;
                text-align: center;
                margin-bottom: 30px;
                font-size: 26px;
            }
            label {
                color: #fff;
                display: block;
                margin-bottom: 8px;
            }
            input[type="email"], input[type="password"] {
                width: 100%;
                padding: 12px;
                margin-bottom: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
                background-color: #f8f9fa;
                font-size: 14px;
            }
            input[type="submit"] {
                background-color: #00d9b7;
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
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Admin Login</h2>
            <form action="admin_login.php" method="POST">
                <label for="email">Email</label>
                <input type="email" name="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" required>

                <input type="submit" value="Login">
                <center><a href="change.php">Forgot Password?</a></center>
            </form>
            <a href="login.php">Go back to User Login</a>
        </div>
    </body>
    </html>