<?php
session_start();
include 'db.php';

// Check if the edit_id is set in the URL
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];

    // Fetch the record to edit
    $sql = "SELECT * FROM tbcreate WHERE Id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if (!$row) {
        echo "<div class='error'>No record found.</div>";
        exit;
    }
} else {
    echo "<div class='error'>Invalid request.</div>";
    exit;
}
// Restrict non-admin users from performing CRUD operations
if ($_SESSION['account_type'] != 'admin') {
    // Check for any CRUD operation: submit (for upload), delete, edit, and file upload
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && 
        (isset($_POST['submit']) || isset($_POST['delete_id']) || isset($_POST['edit_id']) || isset($_FILES['image']))) {
        echo "<script>alert('You are not an admin. You cannot perform CRUD operations.'); window.location.href='create.php';</script>";
        exit();
    }
    
    // For GET request, if trying to access edit page (edit_id)
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['edit_id'])) {
        echo "<script>alert('You are not an admin. You cannot edit records.'); window.location.href='create.php';</script>";
        exit();
    }
}


// Handle image update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $name = $_POST['name'];
    $image = $_FILES['image']['name'];

    if ($image) {
        // Handle the image upload
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo "<div class='error'>File is not an image.</div>";
            $uploadOk = 0;
        }

        // Allow only PNG and JPG file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "<div class='error'>Sorry, only JPG, JPEG, and PNG files are allowed.</div>";
            $uploadOk = 0;
        }

        // Check file size (limit is 5MB)
        if ($_FILES["image"]["size"] > 5000000) {
            echo "<div class='error'>Sorry, your file is too large.</div>";
            $uploadOk = 0;
        }

        // If all checks are passed, upload the file
        if ($uploadOk == 1) {
            // Delete the old image file
            $oldImage = $row['Image'];
            if (file_exists($target_dir . $oldImage)) {
                unlink($target_dir . $oldImage);
            }

            // Move the uploaded file
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                echo "<div class='error'>Sorry, there was an error uploading your file.</div>";
                exit;
            }
        }
    } else {
        // If no new image is uploaded, keep the old image
        $image = $row['Image'];
    }

    // Update the record in the database
    $sqlUpdate = "UPDATE tbcreate SET name='$name', image='$image' WHERE Id=$id";
    if ($conn->query($sqlUpdate) === TRUE) {
        echo "<div class='success'>Successfully updated!</div>";
    } else {
        echo "<div class='error'>Error: " . $conn->error . "</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Image</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your external CSS file -->
    <style>
        /* Add your existing CSS styles here */
        body, html {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa; /* Light background for contrast */
            padding: 20px;
        }

        .form-container {
            max-width: 500px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            margin: 100px auto;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #444;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #444;
        }

        input[type="text"], input[type="file"] {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .success, .error {
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Record</h1>
        <form action="edit.php?edit_id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $row['Name']; ?>" required>

            <label for="image">Select new image (optional):</label>
            <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png">

            <input type="submit" name="update" value="Update">
            <a href="create.php">Back to Create</a>
        </form>
    </div>
</body>
</html>