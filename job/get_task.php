<?php
$mysqli = new mysqli("localhost", "cahp3372_cahyonegoro", "M@ster234", "cahp3372_cahyonegoro");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$task_id = intval($_GET['task_id']);
$stmt = $mysqli->prepare("SELECT * FROM tasks WHERE task_id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();
echo json_encode($task);
$stmt->close();
$mysqli->close();
?>
