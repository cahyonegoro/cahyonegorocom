<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve the document ID from the query string
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Read document details from the file
$document = '';
if (file_exists('documents.txt')) {
    $lines = file('documents.txt');
    foreach ($lines as $line) {
        list($docId, $title, $date) = explode('|', trim($line));
        if ($docId === $id) {
            $document = [
                'title' => htmlspecialchars($title),
                'date' => htmlspecialchars($date)
            ];
            break;
        }
    }
}

if (!$document) {
    die("Document not found.");
}

// Convert date to Indonesian format
function formatDateIndonesian($date) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $dateTime = new DateTime($date);
    $day = $dateTime->format('j');
    $month = $months[(int)$dateTime->format('n')];
    $year = $dateTime->format('Y');

    return "$day $month $year";
}

$formattedDate = formatDateIndonesian($document['date']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 80%;
            max-width: 600px;
            margin: 20px;
            text-align: center;
        }
        h1 {
            font-size: 1.8em;
            color: #007bff;
            margin-bottom: 20px;
        }
        .logo {
            margin-bottom: 20px;
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
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        td {
            background-color: #fafafa;
        }
        .header-cell {
            text-align: center;
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            font-size: 1.2em;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="img/bsre-logo-full.png" alt="Logo" style="max-width: 100%; height: auto;">
        </div>
        <h1>Document Verification</h1>
        <table>
            <tr>
                <td colspan="2" class="header-cell">Dokumen ini telah di tandatangan secara digital oleh:</td>
            </tr>
            <tr>
                <th>Field</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>Nama</td>
                <td>Muhammad Noordiansyah Cahyo Negoro, S. Kom.</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>198904092019021002</td>
            </tr>
            <tr>
                <td>Instansi</td>
                <td>BPH Migas Kementerian ESDM</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td><?php echo $formattedDate; ?></td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td><?php echo $document['title']; ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
