<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'phpqrcode/qrlib.php'; // Adjust the path as needed

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
    $title = $_POST['title'];
    $date = $_POST['date'] ?: date('Y-m-d'); // Default to today if no date provided
    $id = uniqid(); // Generate a unique ID for the document

    // Generate QR code
    $qrContent = "https://cahyonegoro.com/documents/verify.php?id=$id";
    $qrFile = "qr_codes/$id.png";
    QRcode::png($qrContent, $qrFile, 'L', 4, 2);

    // Save document information to a file
    $file = fopen('documents.txt', 'a');
    fwrite($file, "$id|$title|$date\n");
    fclose($file);
}

// Handle document deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $deleteId = $_POST['id'];

    // Read all lines from the documents file
    $lines = file('documents.txt');
    $newLines = [];

    foreach ($lines as $line) {
        list($id, $title, $date) = explode('|', trim($line));
        if ($id != $deleteId) {
            $newLines[] = $line; // Keep line if ID does not match
        } else {
            // Delete QR code file
            $qrFile = "qr_codes/$id.png";
            if (file_exists($qrFile)) {
                unlink($qrFile);
            }
        }
    }

    // Write back the remaining lines
    file_put_contents('documents.txt', implode("", $newLines));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Document</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            max-width: 100px;
            height: auto;
        }
        .delete-btn {
            color: red;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Create Document</h1>
    <form action="index.php" method="POST">
        <input type="hidden" name="action" value="create">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <br>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
        <br>
        <input type="submit" value="Create Document">
    </form>
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
</body>
</html>
