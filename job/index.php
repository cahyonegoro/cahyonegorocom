<?php
// Database connection
$mysqli = new mysqli("localhost", "cahp3372_cahyonegoro", "M@ster234", "cahp3372_cahyonegoro");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add new task
    if ($_POST['action'] == 'create') {
        $stmt = $mysqli->prepare("INSERT INTO tasks (task_name, description, start_date, due_date, priority, status, category_id, progress) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssii", $_POST['task_name'], $_POST['description'], $_POST['start_date'], $_POST['due_date'], $_POST['priority'], $_POST['status'], $_POST['category'], $_POST['progress']);
        $stmt->execute();
        $stmt->close();
    }

    // Update task
    if ($_POST['action'] == 'update') {
        $stmt = $mysqli->prepare("UPDATE tasks SET task_name=?, description=?, start_date=?, due_date=?, completion_date=?, priority=?, status=?, category_id=?, progress=? WHERE task_id=?");
        $stmt->bind_param("sssssssiii", $_POST['task_name'], $_POST['description'], $_POST['start_date'], $_POST['due_date'], $_POST['completion_date'], $_POST['priority'], $_POST['status'], $_POST['category'], $_POST['progress'], $_POST['task_id']);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch tasks and categories
$tasks = $mysqli->query("SELECT tasks.*, categories.category_name FROM tasks LEFT JOIN categories ON tasks.category_id = categories.category_id ORDER BY tasks.due_date");
$categories = $mysqli->query("SELECT * FROM categories");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Monitoring</title>
    <style>
        /* Add your CSS styling here */
    </style>
</head>
<body>
<h1>Task Monitoring</h1>

<!-- Form to add a new task -->
<form action="index.php" method="POST">
    <input type="hidden" name="action" value="create">
    <label for="task_name">Task Name:</label>
    <input type="text" id="task_name" name="task_name" required><br>

    <label for="description">Description:</label>
    <textarea id="description" name="description"></textarea><br>

    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date"><br>

    <label for="due_date">Due Date:</label>
    <input type="date" id="due_date" name="due_date"><br>

    <label for="priority">Priority:</label>
    <select id="priority" name="priority">
        <option value="Low">Low</option>
        <option value="Medium" selected>Medium</option>
        <option value="High">High</option>
    </select><br>

    <label for="status">Status:</label>
    <select id="status" name="status">
        <option value="Not Started" selected>Not Started</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
        <option value="On Hold">On Hold</option>
    </select><br>

    <label for="category">Category:</label>
    <select id="category" name="category">
        <?php while ($row = $categories->fetch_assoc()): ?>
            <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
        <?php endwhile; ?>
    </select><br>

    <label for="progress">Progress:</label>
    <input type="number" id="progress" name="progress" min="0" max="100"><br>

    <input type="submit" value="Add Task">
</form>

<!-- List of tasks -->
<h2>Tasks</h2>
<table>
    <tr>
        <th>Task ID</th>
        <th>Task Name</th>
        <th>Description</th>
        <th>Start Date</th>
        <th>Due Date</th>
        <th>Completion Date</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Category</th>
        <th>Progress</th>
        <th>Actions</th>
    </tr>
    <?php while ($task = $tasks->fetch_assoc()): ?>
        <tr>
            <td><?php echo $task['task_id']; ?></td>
            <td><?php echo $task['task_name']; ?></td>
            <td><?php echo $task['description']; ?></td>
            <td><?php echo $task['start_date']; ?></td>
            <td><?php echo $task['due_date']; ?></td>
            <td><?php echo $task['completion_date']; ?></td>
            <td><?php echo $task['priority']; ?></td>
            <td><?php echo $task['status']; ?></td>
            <td><?php echo $task['category_name']; ?></td>
            <td><?php echo $task['progress']; ?>%</td>
            <td>
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                    <input type="submit" value="Edit">
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
