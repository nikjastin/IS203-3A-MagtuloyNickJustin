<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipientEmail = $_POST['email'];
    $messageBody = $_POST['message'];

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bakachallengeryan@gmail.com'; // Your Gmail address
        $mail->Password = 'gxho vrur radg vcwv'; // Gmail password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('bakachallengeryan@gmail.com', 'Nick Justin Magtuloy');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Message from PHP Mailer';
        $mail->Body = $messageBody;

        $mail->send();
        echo "<script>alert('Email Message Successfully sent');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email</title>
    <style>
        body, html {
            font-family: Arial, sans-serif;
            background-image: url('uploads/email.jpg'); /* Path to your image */
            background-size: cover; /* Cover the entire viewport */
            background-position: center; /* Center the image */
            background-repeat: no-repeat; /* Prevent repeating */
            padding: 20px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box; 
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f0f5;
            display: flex;
            justify-content: left;
            align-items: left;
            height: 100vh;
        }

        .navbar {
            background-color: #333;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            background-color: #444;
            border-radius: 5px;
        }

        .email-form {
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            margin-top: 80px;
        }

        .email-form h2 {
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .email-form label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            text-align: left;
            color: #555;
        }

        .email-form input[type="text"], .email-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            background-color: #fafafa;
            transition: border-color 0.3s ease;
        }

        .email-form input[type="text"]:focus, .email-form textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        .email-form textarea {
            resize: none;
            height: 120px;
        }

        .email-form input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .email-form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        @media (max-width: 500px) {
            .email-form {
                width: 100%;
                padding: 20px;
                margin: 40px;
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

    <div class="email-form">
        <h2>Send Email</h2>
        <form action="sms_notification.php" method="post">
            <label for="email">Recipient's Gmail:</label>
            <input type="text" id="email" name="email" placeholder="Enter recipient's Gmail" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" placeholder="Enter your message" rows="5" required></textarea>

            <input type="submit" value="Send Email">
        </form>
    </div>

</body>
</html>
