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
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT Password, Firstname, Lastname FROM tbregistration WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the email exists
    if ($stmt->num_rows == 1) {
        // Bind result
        $stmt->bind_result($hashed_password, $firstname, $lastname);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Start session
            session_start();
            $_SESSION['email'] = $email; // Store email in session
            $_SESSION['firstname'] = $firstname; // Store first name in session
            $_SESSION['lastname'] = $lastname; // Store last name in session

            // Display success message before redirecting
            echo "<script>alert('Login successful!');</script>";
            echo "<script>window.location.href = 'create.php';</script>"; // Redirect to the create page
            exit();
        } else {
            // Show invalid credentials message
            echo "<script>alert('Invalid email or password.');</script>";
        }
        } else {
        // Show invalid credentials message
        echo "<script>alert('Invalid email or password.');</script>";
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
    <title>Login</title>
    <style>
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
            text-align: center; /* Ensure the Login header stays centered */
        }

        label {
            display: block;
            text-align: left; /* Aligns labels to the left */
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        input[type="text"]:focus, input[type="password"]:focus {
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
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="text" name="email" required placeholder="Enter your email">

            <label for="password">Password:</label>
            <input type="password" name="password" required placeholder="Enter your password">

            <input type="submit" value="Login">
        </form>
        <center><a href="change.php">Forgot Password?</a></center>
        <div class="back-button-container">
            <button type="button" class="back-button" onclick="window.location.href='back.php'">Back</button>
        </div>
    </div>
</body>
</html>