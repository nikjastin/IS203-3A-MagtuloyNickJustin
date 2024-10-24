<?php
session_start();
include 'db.php';

// Restrict non-admin users from performing profile picture updates
if ($_SESSION['account_type'] != 'admin') {
    // Check for any profile picture operation (submit for upload)
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
        echo "<script>alert('You are not an admin. You cannot change the profile picture.'); window.location.href='create.php';</script>";
        exit();
    }
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    if (isset($_FILES["profile_image"]["tmp_name"]) && $_FILES["profile_image"]["tmp_name"] != "") {
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "<div class='error'>File is not an image.</div>";
            $uploadOk = 0;
        }
    } else {
        echo "<div class='error'>No file uploaded.</div>";
        $uploadOk = 0;
    }

    // Allow only PNG and JPG file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "<div class='error'>Sorry, only JPG, JPEG, and PNG files are allowed.</div>";
        $uploadOk = 0;
    }

    // Check file size (limit is 5MB)
    if ($_FILES["profile_image"]["size"] > 5000000) {
        echo "<div class='error'>Sorry, your file is too large.</div>";
        $uploadOk = 0;
    }

    // If all checks are passed, upload the file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = basename($_FILES["profile_image"]["name"]);
            $_SESSION['profile_image'] = $profile_image; // Update session variable

            // Optionally, you can save the new profile image to the database here
            // $sql = "UPDATE users SET profile_image = '$profile_image' WHERE user_id = {$_SESSION['user_id']}";
            // $conn->query($sql);

            echo "<div class='success'>Profile image updated successfully.</div>";
        } else {
            echo "<div class='error'>Sorry, there was an error uploading your file.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        /* Add your styles here */
    </style>
</head>
<style>
/* Global styles */
body, html {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa; /* Light background color */
    margin: 0;
    padding: 0;
}

/* Profile Container */
.profile-container {
    max-width: 600px; /* Set a max width for the profile section */
    margin: 50px auto; /* Center the container */
    background-color: white; /* White background for the container */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    padding: 30px; /* Padding inside the container */
    text-align: center; /* Center align text */
}

/* Heading Styles */
h1 {
    color: #333; /* Dark gray for the heading */
}

/* Profile Picture Styles */
.profile-container img {
    width: 150px; /* Set image width */
    height: 150px; /* Set image height */
    border-radius: 50%; /* Circular shape */
    object-fit: cover; /* Cover the entire area */
    border: 3px solid #007bff; /* Blue border */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
    transition: transform 0.3s; /* Smooth scaling */
}

/* Profile Picture Hover Effect */
.profile-container img:hover {
    transform: scale(1.05); /* Slightly enlarge on hover */
}

/* Label Styles */
label {
    font-weight: bold; /* Bold text for labels */
    color: #333; /* Dark gray color */
    display: block; /* Block display for spacing */
    margin: 20px 0 5px; /* Margin for spacing */
}

/* Input Styles */
input[type="file"] {
    width: 100%; /* Full width */
    padding: 12px; /* Padding for input */
    border: 1px solid #ddd; /* Light border */
    border-radius: 5px; /* Rounded corners */
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1); /* Inner shadow */
    margin-bottom: 20px; /* Space below input */
}

/* Button Styles */
input[type="submit"] {
    background-color: #007bff; /* Blue background */
    color: white; /* White text */
    border: none; /* No border */
    padding: 12px 20px; /* Padding for button */
    border-radius: 5px; /* Rounded corners */
    font-size: 16px; /* Font size */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s ease; /* Smooth transition */
}

input[type="submit"]:hover {
    background-color: #0056b3; /* Darker shade on hover */
}

/* Link Styles */
a {
    display: inline-block; /* Inline block for spacing */
    margin-top: 20px; /* Space above link */
    color: #007bff; /* Blue color */
    text-decoration: none; /* No underline */
    font-weight: bold; /* Bold text */
}

a:hover {
    text-decoration: underline; /* Underline on hover */
}
</style>
<body>
    <h1>Update Profile Picture</h1>
    <div style="text-align: center;">
        <img src="uploads/<?php echo isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'default.png'; ?>" alt="Profile Picture" style="width: 200px; height: 200px; border-radius: 50%;">
    </div>
    <form action="profile.php" method="post" enctype="multipart/form-data">
        <label for="profile_image">Select a new profile image:</label>
        <input type="file" id="profile_image" name="profile_image" accept=".jpg, .jpeg, .png" required>
        <input type="submit" name="update_profile" value="Update Profile Picture">
    </form>
    <a href="create.php">Back to Upload Image</a>
</body>
</html>