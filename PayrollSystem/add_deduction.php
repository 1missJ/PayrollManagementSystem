<?php 
session_start();
include 'connection.php';

// Handle form submission for adding a new custom deduction
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_custom_deduction'])) {
    $deduction_name = $_POST['deduction_name'];

    // Insert new deduction into the database
    $query = "INSERT INTO custom_deductions (name) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $deduction_name);

    if ($stmt->execute()) {
        echo "<script>alert('Custom Deduction Added Successfully!'); window.location.href='add_deduction.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }

    $stmt->close();
}

// Handle removal of a custom deduction
if (isset($_GET['remove'])) {
    $deduction_id = (int)$_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM custom_deductions WHERE id = ?");
    $stmt->bind_param("i", $deduction_id);
    if ($stmt->execute()) {
        echo "<script>alert('Custom Deduction Removed Successfully!'); window.location.href='add_deduction.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Custom Deduction</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h2, h3 {
            text-align: center;
            color: #ffa07a;
        }

        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #ffa07a;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        button:hover {
            background-color: #ff8c42;
        }

        h3 {
            margin-top: 50px;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            background-color: #fff;
            margin: 10px 0;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        li a {
            color: #ff6347;
            text-decoration: none;
            font-weight: bold;
        }

        li a:hover {
            text-decoration: underline;
        }

        /* Back Button Styling */
        .back-btn {
            display: inline-block;
            background-color: #008CBA;
            color: white;
            padding: 10px 20px;
            margin: 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
        }

        .back-btn:hover {
            background-color: #005f6b;
        }
    </style>
</head>
<body>

    <!-- Back Button -->
    <a href="deductionList.php" class="back-btn">Back to Deduction List</a>
        
    <h2>Add Custom Deduction</h2>
    <form method="POST" action="add_deduction.php">
        <label for="deduction-name">Deduction Name:</label>
        <input type="text" id="deduction-name" name="deduction_name" required>
        <button type="submit" name="add_custom_deduction">Add Deduction</button>
    </form>

    <h3>Existing Custom Deductions</h3>
    <ul>
        <?php
        // Fetch all custom deductions
        $result = $conn->query("SELECT * FROM custom_deductions");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<li>" . $row['name'] . " <a href='add_deduction.php?remove=" . $row['id'] . "'>Remove</a></li>";
            }
        } else {
            echo "<li>No custom deductions found.</li>";
        }
        ?>
    </ul>

</body>
</html>
