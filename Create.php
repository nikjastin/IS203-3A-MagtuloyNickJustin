<?php
session_start();
include 'db.php';

// Check if user is logged in and fetch the role
if (!isset($_SESSION['account_type'])) {
    $_SESSION['account_type'] = 'guest'; // Default to guest if not logged in
}
$account_type = $_SESSION['account_type'];

// Restrict non-admin users from performing CRUD operations
if ($account_type != 'admin') {
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

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $name = $_POST['name'];

    // Handle the image upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    if (isset($_FILES["image"]["tmp_name"]) && $_FILES["image"]["tmp_name"] != "") {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
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

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "<div class='error'>Sorry, file already exists.</div>";
        $uploadOk = 0;
    }

    // Check file size (limit is 5MB)
    if ($_FILES["image"]["size"] > 5000000) {
        echo "<div class='error'>Sorry, your file is too large.</div>";
        $uploadOk = 0;
    }

    // If all checks are passed, upload the file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = basename($_FILES["image"]["name"]);
            $sql = "INSERT INTO tbcreate (name, image) VALUES ('$name', '$image')";

            if ($conn->query($sql) === TRUE) {
                echo "<div class='success'>New record created successfully</div>";
            } else {
                echo "<div class='error'>Error: " . $sql . "<br>" . $conn->error . "</div>";
            }
        } else {
            echo "<div class='error'>Sorry, there was an error uploading your file.</div>";
        }
    }
}

// Handle record deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // Fetch the record to get the image name
    $sql = "SELECT * FROM tbcreate WHERE Id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row) {
        // Delete the record from the database
        $image = $row['Image']; // Get the image name
        $sqlDelete = "DELETE FROM tbcreate WHERE Id = $id";

        if ($conn->query($sqlDelete) === TRUE) {
            // Delete the image file from the server
            $target_file = "uploads/" . $image;
            if (file_exists($target_file)) {
                unlink($target_file); // Remove the image file
            }
            echo "<div class='success'>Record deleted successfully</div>";
        } else {
            echo "<div class='error'>Error: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='error'>No record found.</div>";
    }
}

// Fetch user details if logged in
if (isset($_SESSION['firstname']) && isset($_SESSION['lastname']) && isset($_SESSION['role'])) {
    $firstName = $_SESSION['firstname'];
    $lastName = $_SESSION['lastname'];
} else {
    $firstName = "Admin";
    $lastName = "";
    $role = "guest"; // Handle guest case
}

// Fetch all records from the database
$sql = "SELECT * FROM tbcreate"; 
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
    <style>
        /* Global styles */
        body, html {
            font-family: Arial, sans-serif;
            background-image: url('uploads/cloud.jpg'); /* Path to your image */
            background-size: cover; /* Cover the entire viewport */
            background-position: center; /* Center the image */
            background-repeat: no-repeat; /* Prevent repeating */
            padding: 20px;
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

        /* Search bar container */
        .search-container {
            text-align: center;
            margin-top: 20px;
        }

        .search-bar {
            width: 50%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 20px;
        }

        .search-button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-button:hover {
            background-color: #218838;
        }

        /* Search label */
        .search-label {
            color: #444;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        /* Form styles */
        .form-container {
            max-width: 500px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            margin: 100px auto;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-out;
        }

        h1, h2 {
            text-align: left;
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
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        input[type="submit"] {
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #218838;
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

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        img {
            max-width: 100px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        button, input[type='submit'][value='Delete'], input[type='submit'][value='Edit'] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100px;
            text-align: center;
        }

        button:hover, input[type='submit'][value='Delete']:hover, input[type='submit'][value='Edit']:hover {
            background-color: #0056b3;
        }

        input[type='submit'][value='Delete'] {
            background-color: #dc3545;
        }
        /* Profile Picture Styles */
.profile-container {
    text-align: center; 
    margin-top: 80px; 
}

.profile-container img {
    border-radius: 50%; /* Makes the image circular */
    width: 150px; /* Set the width */
    height: 150px; /* Set the height */
    object-fit: cover; /* Ensures the image covers the area without distortion */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Adds a shadow for depth */
    border: 3px solid #fff; /* White border for better visibility */
    transition: transform 0.3s; /* Smooth scale effect */
}

.profile-container img:hover {
    transform: scale(1.05); /* Slightly enlarges the image on hover */
}

.profile-container h2 {
    color: #444; /* Color for the username */
    margin-top: 10px; /* Space above the name */
}
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
   <!-- Navigation bar with role-based greeting -->
<div class="navbar">    
    <a href="create.php">Home</a>
    
    <?php if ($account_type == 'admin') { ?>
        <a href="sms_notification.php">Email Notification</a>
        <a href="sms.php">SMS</a>
    <?php } ?>
    
    <a href="logout.php">Logout</a>
        
        <!-- Check if user is logged in and display their name and type -->
<?php
if (isset($_SESSION['firstname']) && isset($_SESSION['lastname'])) {
    $firstName = $_SESSION['firstname'];
    $lastName = $_SESSION['lastname'];
    $accountType = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : ''; //Default to user if not set
    
    // Display greeting based on user type
    if ($accountType == 'admin') {
        echo "<a href='#'>Welcome, Admin $firstName $lastName</a>";
    } else {
        echo "<a href='#'>Welcome, User $firstName $lastName</a>";
    }
} else {
    echo "<a href='#'>Welcome, Guest</a>";
}
?>
</div>
    <!-- Profile Picture Section -->
<div class="profile-container">
    <a href="profile.php">
        <img src="uploads/<?php echo isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'default.png'; ?>" alt="Profile Picture" style="width: 350px; height: 150px; border-radius: 50%;">
    </a>
</div>

<!-- Search Bar -->
<div class="search-container">
    <form method="get" action="create.php">
        <input type="text" name="search" class="search-bar" placeholder="Search by name...">
        <button type="submit" class="search-button">Search</button>
    </form>
    <div class="search-label">Search</div>
</div>

<?php
// Modify the SQL query to filter based on search input
$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($search != '') {
    $sql = "SELECT * FROM tbcreate WHERE Name LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM tbcreate";
}

$result = $conn->query($sql);
?>

</div>
</form>
    <div class="form-container">
        <h1>Upload Image</h1>
        <form action="create.php" method="post" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="image">Select image to upload:</label>
            <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png" required>

            <input type="submit" name="submit" value="Upload Image">
        </form>

        <h2>Uploaded Records</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>

            <?php if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['Name']; ?></td>
                        <td><img src="uploads/<?php echo $row['Image']; ?>" alt="<?php echo $row['Name']; ?>"></td>
                        <td>
                            <!-- Edit Button -->
                            <form action="edit.php" method="get">
                            <input type="hidden" name="edit_id" value="<?php echo $row['Id']; ?>">
                            <input type="submit" value="Edit">
                            </form>
                            <!-- Delete Button -->
                            <form action="create.php" method="post" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                <input type="hidden" name="delete_id" value="<?php echo $row['Id']; ?>">
                                <input type="submit" value="Delete">
                            </form>
                        </td>
                    </tr>
                <?php 
            }
        } 
            else 
        { 
            ?>
                <tr>
                    <td colspan="3">No records found</td>
                </tr>
            <?php
        } 
         
        ?>
        </table>
    </div>
</body>
</html>