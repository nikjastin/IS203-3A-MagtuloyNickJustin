<?php
require 'vendor/autoload.php'; // Include the Twilio SDK

use Twilio\Rest\Client;

// Your Twilio credentials
$sid = 'ACc3f3846f55d658007633932dc8760752'; // Your Twilio Account SID
$token = '9443489636435b1ca1398266ac93a9fa';  // Your Twilio Auth Token
$twilioNumber = '+13367934874'; // Your Twilio phone number

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $number = $_POST['number']; // Phone number to send the message to
    $message = $_POST['message']; // Message content

    // Create a Twilio client
    $client = new Client($sid, $token);

    try {
        // Send SMS
        $client->messages->create(
            $number, // The number you are sending to
            [
                'from' => $twilioNumber, // Your Twilio phone number
                'body' => $message // Message content
            ]
        );
        echo "<div class='success'>Message sent successfully!</div>";
    } catch (Exception $e) {
        echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body, html {
            font-family: Arial, sans-serif;
            background-image: url('uploads/sms.jpg'); /* Path to your image */
            background-size: cover; /* Cover the entire viewport */
            background-position: center; /* Center the image */
            background-repeat: no-repeat; /* Prevent repeating */
            padding: 20px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Navigation bar styles */
        .navbar {
            background-color: #333;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: left;
            z-index: 1000;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            font-weight: 500;
            text-transform: uppercase;
            transition: background 0.3s ease;
        }

        .navbar a:hover {
            background-color: #555;
            border-radius: 5px;
        }

        /* Form container */
        .form-container {
            background-color: #fff;
            width: 400px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.5rem;
            font-weight: 600;
        }

        label {
            display: block;
            margin-top: 10px;
            color: #333;
            font-weight: 500;
        }

        input[type="text"], textarea {
            width: 90%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        textarea {
            resize: none;
            height: 100px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 6px;
            margin-top: 15px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Success/Error Message */
        .success, .error {
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile responsiveness */
        @media (max-width: 500px) {
            .form-container {
                width: 100%;
                margin: 0 20px;
            }
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar">
        <a href="create.php">Home</a>
        <a href="sms_notification.php">Email Notification</a>
        <a href="sms.php">SMS</a>
        <a href="login.php">Logout</a>
    </div>

<!-- SMS Form -->
<div class="form-container">
    <h1>Send SMS</h1>
    <form action="sms.php" method="post">
        <label for="number">Phone Number:</label>
        <input type="text" name="number" id="number" required placeholder="Enter phone number">

        <label for="message">Message:</label>
        <textarea name="message" id="message" rows="4" required placeholder="Type your message here"></textarea>

        <input type="submit" value="Send SMS">
    </form>
</div>

</body>
</html>