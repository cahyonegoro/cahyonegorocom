<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        $title = trim($_POST['title']);
        $date = trim($_POST['date']);

        if (!empty($title) && !empty($date)) {
            // Generate a unique ID for the document
            $id = time(); // You can use time or any other method to generate a unique ID

            // Prepare the data to be written to the file
            $documentLine = "$id|$title|$date" . PHP_EOL;

            // Write the data to the text file
            file_put_contents('documents.txt', $documentLine, FILE_APPEND);

            // Redirect to avoid form resubmission
            header("Location: index.php");
            exit;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $idToDelete = $_POST['id'];

        // Read the current lines from the file
        $lines = file('documents.txt', FILE_IGNORE_NEW_LINES);

        // Filter out the line with the matching ID
        $newLines = array_filter($lines, function ($line) use ($idToDelete) {
            return strpos($line, $idToDelete . '|') !== 0;
        });

        // Write the remaining lines back to the file
        file_put_contents('documents.txt', implode(PHP_EOL, $newLines) . PHP_EOL);

        // Redirect to avoid form resubmission
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Document</title>
    <!-- Add your styles here -->
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
            $lines = file('documents.txt', FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                list($id, $title, $date) = explode('|', $line);
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
