<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Replace with your credentials
    $correct_username = 'cahyonegoro';
    $correct_password = 'M@ster234';

    if ($username === $correct_username && $password === $correct_password) {
        $_SESSION['loggedin'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 40px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        td {
            background-color: #fff;
            color: #555;
        }
        .delete-btn {
            color: red;
            text-decoration: none;
            cursor: pointer;
            padding: 8px 12px;
            border: 1px solid red;
            border-radius: 4px;
            background-color: #fdd;
        }
        .delete-btn:hover {
            background-color: #fbb;
        }
        img {
            max-width: 100px;
            height: auto;
            border-radius: 4px;
        }
        .logout {
            text-align: right;
            margin-bottom: 20px;
        }
        .logout a {
            color: #333;
            text-decoration: none;
            font-size: 14px;
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f2f2f2;
        }
        .logout a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Create Document</h1>
    <form action="index.php" method="POST">
        <input type="hidden" name="action" value="create">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
        <input type="submit" value="Create Document">
    </form>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

    <h2>Document List</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Date</th>
            <th>QR Code</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (file_exists('documents.txt')) {
            $lines = file('documents.txt');
            foreach ($lines as $line) {
                list($id, $title, $date) = explode('|', trim($line));
                $qrFile = "qr_codes/$id.png";
                echo "<tr>";
                echo "<td>" . htmlspecialchars($id) . "</td>";
                echo "<td>" . htmlspecialchars($title) . "</td>";
                echo "<td>" . htmlspecialchars($date) . "</td>";
                echo "<td>";
                if (file_exists($qrFile)) {
                    echo "<a href='$qrFile' download='qr_code_$id.png'><img src='$qrFile' alt='QR Code'></a>";
                } else {
                    echo "No QR Code";
                }
                echo "</td>";
                echo "<td>";
                echo "<form action='index.php' method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='action' value='delete'>";
                echo "<input type='hidden' name='id' value='" . htmlspecialchars($id) . "'>";
                echo "<input type='submit' value='Delete' class='delete-btn'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No documents available.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
