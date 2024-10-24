<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare a DELETE SQL statement
    $sql = "DELETE FROM tbcreate WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id); // Bind the parameter

    if ($stmt->execute()) {
        echo "<div class='success'>Record deleted successfully.</div>";
    } else {
        echo "<div class='error'>Error deleting record: " . $conn->error . "</div>";
    }

    $stmt->close();
}

header("Location: create.php"); // Redirect to create.php after deletion
exit();
?>
