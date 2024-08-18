<?php
session_start();

// Database connection
$mysqli = new mysqli("localhost", "cahp3372_cahyonegoro", "M@ster234", "cahp3372_cahyonegoro");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Add new task
                $stmt = $mysqli->prepare("INSERT INTO tasks (task_name, description, start_date, due_date, priority, status, category_id, progress) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssii", $_POST['task_name'], $_POST['description'], $_POST['start_date'], $_POST['due_date'], $_POST['priority'], $_POST['status'], $_POST['category'], $_POST['progress']);
                $stmt->execute();
                $stmt->close();
                break;

            case 'update':
                // Update task
                $stmt = $mysqli->prepare("UPDATE tasks SET task_name=?, description=?, start_date=?, due_date=?, completion_date=?, priority=?, status=?, category_id=?, progress=? WHERE task_id=?");
                $stmt->bind_param("sssssssiii", $_POST['task_name'], $_POST['description'], $_POST['start_date'], $_POST['due_date'], $_POST['completion_date'], $_POST['priority'], $_POST['status'], $_POST['category'], $_POST['progress'], $_POST['task_id']);
                $stmt->execute();
                $stmt->close();
                break;

            case 'delete':
                // Delete task
                if (isset($_POST['task_id'])) {
                    $task_id = $_POST['task_id'];
                    $mysqli->query("DELETE FROM tasks WHERE task_id = $task_id");
                }
                break;

            case 'complete':
                // Mark task as completed
                if (isset($_POST['task_id'])) {
                    $task_id = $_POST['task_id'];
                    $mysqli->query("UPDATE tasks SET status='Completed', progress='100' WHERE task_id = $task_id");
                }
                break;
        }
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
    <title>Task Monitoring Job Cahyo Negoro</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f9;
            color: #333;
        }

        h1 {
            color: #007bff;
            text-align: center;
            margin: 20px 0;
        }

        button, input[type="submit"] {
            cursor: pointer;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 16px;
            color: white;
            transition: background-color 0.3s ease;
        }

        button:hover, input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Add Task Button */
        .toggle-btn {
            background-color: #007bff;
        }

        /* Form Styles */
        .hidden {
            display: none;
        }

        #add-task-form, #edit-task-modal .modal-content {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
        }

        h2 {
            color: #007bff;
            margin-bottom: 15px;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .form-control label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-control input, .form-control textarea, .form-control select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-control input[type="date"], .form-control input[type="number"], .form-control select {
            font-size: 16px;
        }

        .form-control input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-control input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        td {
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Action Buttons */
        .action-btn {
            padding: 8px 12px;
            margin-right: 5px;
            border-radius: 5px;
            font-size: 14px;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .action-btn.edit {
            background-color: #28a745;
        }

        .action-btn.edit:hover {
            background-color: #218838;
        }

        .action-btn.delete {
            background-color: #dc3545;
        }

        .action-btn.delete:hover {
            background-color: #c82333;
        }

        .action-btn.complete {
            background-color: #ffc107;
        }

        .action-btn.complete:hover {
            background-color: #e0a800;
        }

        /* Progress Bar Styles */
        .progress-container {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .progress-bar {
            background-color: #4caf50;
            height: 20px;
            line-height: 20px;
            color: white;
            text-align: center;
            border-radius: 10px 0 0 10px; /* Rounded corners only on the left side */
            font-weight: bold;
            transition: width 0.3s ease;
        }

        .progress-text {
            display: inline-block;
            position: relative;
            z-index: 1;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
            }
        }


    </style>
    <script>
        function toggleForm() {
            const form = document.getElementById('add-task-form');
            form.classList.toggle('hidden');
        }

        function openEditModal(taskId) {
            fetch(`get_task.php?task_id=${taskId}`)
                .then(response => response.json())
                .then(task => {
                    document.getElementById('edit-task-id').value = task.task_id;
                    document.getElementById('edit-task_name').value = task.task_name;
                    document.getElementById('edit-description').value = task.description;
                    document.getElementById('edit-start_date').value = task.start_date;
                    document.getElementById('edit-due_date').value = task.due_date;
                    document.getElementById('edit-completion_date').value = task.completion_date;
                    document.getElementById('edit-priority').value = task.priority;
                    document.getElementById('edit-status').value = task.status;
                    document.getElementById('edit-category').value = task.category_id;
                    document.getElementById('edit-progress').value = task.progress;
                    document.getElementById('edit-task-modal').style.display = 'block';
                });
        }

        function closeEditModal() {
            document.getElementById('edit-task-modal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('edit-task-modal')) {
                closeEditModal();
            }
        }

        document.querySelectorAll('.complete-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskId = this.getAttribute('data-task-id');
                fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=complete&task_id=${taskId}`
                }).then(response => response.text()).then(() => location.reload());
            });
        });
    </script>
</head>
<body>

<h1>Task Monitoring</h1>
<button class="toggle-btn" onclick="toggleForm()">Add New Task</button>

<div id="add-task-form" class="hidden">
    <h2>Add New Task</h2>
    <form action="index.php" method="POST">
        <input type="hidden" name="action" value="create">
        <div class="form-control">
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name" required>
        </div>
        <div class="form-control">
            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>
        </div>
        <div class="form-control">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date">
        </div>
        <div class="form-control">
            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date">
        </div>
        <div class="form-control">
            <label for="priority">Priority:</label>
            <select id="priority" name="priority">
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
            </select>
        </div>
        <div class="form-control">
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Not Started">Not Started</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
                <option value="On Hold">On Hold</option>
            </select>
        </div>
        <div class="form-control">
            <label for="category">Category:</label>
            <select id="category" name="category">
                <?php
                $categories->data_seek(0); // Reset result pointer
                while ($row = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-control">
            <label for="progress">Progress:</label>
            <input type="number" id="progress" name="progress" min="0" max="100">
        </div>
        <div class="form-control">
            <input type="submit" value="Add Task">
        </div>
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
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
            <td>
                <?php
                $date = new DateTime($task['start_date']);
                $formatter = new IntlDateFormatter(
                    'id_ID',
                    IntlDateFormatter::LONG,
                    IntlDateFormatter::NONE,
                    'Asia/Jakarta',
                    IntlDateFormatter::GREGORIAN,
                    'dd MMMM yyyy'
                );
                echo $formatter->format($date);
                ?>
            </td>
            <td>
                <?php
                $date = new DateTime($task['due_date']);
                $formatter = new IntlDateFormatter(
                    'id_ID',
                    IntlDateFormatter::LONG,
                    IntlDateFormatter::NONE,
                    'Asia/Jakarta',
                    IntlDateFormatter::GREGORIAN,
                    'dd MMMM yyyy'
                );
                echo $formatter->format($date);
                ?>
            </td>
            <td>
                <?php
                if (!empty($task['completion_date'])) {
                    $date = new DateTime($task['completion_date']);
                    $formatter = new IntlDateFormatter(
                        'id_ID',
                        IntlDateFormatter::LONG,
                        IntlDateFormatter::NONE,
                        'Asia/Jakarta',
                        IntlDateFormatter::GREGORIAN,
                        'dd MMMM yyyy'
                    );
                    echo $formatter->format($date);
                }
                ?>
            </td>
            <td><?php echo $task['priority']; ?></td>
            <td><?php echo $task['status']; ?></td>
            <td><?php echo $task['category_name']; ?></td>
            <td>
                <div class="progress-container">
                    <div class="progress-bar" style="width: <?php echo $task['progress']; ?>%;">
                        <span class="progress-text"><?php echo $task['progress']; ?>%</span>
                    </div>
                </div>
            </td>

            <td>
                <button class="action-btn edit" onclick="openEditModal(<?php echo $task['task_id']; ?>)">Edit</button>
                <form style="display:inline;" action="index.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                    <input type="submit" class="action-btn delete" value="Delete">
                </form>
                <form style="display:inline;" action="index.php" method="POST">
                    <input type="hidden" name="action" value="complete">
                    <input type="hidden" name="progress" value="100">
                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                    <input type="submit" class="action-btn complete" value="Complete">
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<!-- Edit Task Modal -->
<div id="edit-task-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Task</h2>
        <form id="edit-task-form" action="index.php" method="POST">
            <input type="hidden" id="edit-task-id" name="task_id">
            <input type="hidden" name="action" value="update">
            <div class="form-control">
                <label for="edit-task_name">Task Name:</label>
                <input type="text" id="edit-task_name" name="task_name" required>
            </div>
            <div class="form-control">
                <label for="edit-description">Description:</label>
                <textarea id="edit-description" name="description"></textarea>
            </div>
            <div class="form-control">
                <label for="edit-start_date">Start Date:</label>
                <input type="date" id="edit-start_date" name="start_date">
            </div>
            <div class="form-control">
                <label for="edit-due_date">Due Date:</label>
                <input type="date" id="edit-due_date" name="due_date">
            </div>
            <div class="form-control">
                <label for="edit-completion_date">Completion Date:</label>
                <input type="date" id="edit-completion_date" name="completion_date">
            </div>
            <div class="form-control">
                <label for="edit-priority">Priority:</label>
                <select id="edit-priority" name="priority">
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="form-control">
                <label for="edit-status">Status:</label>
                <select id="edit-status" name="status">
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="On Hold">On Hold</option>
                </select>
            </div>
            <div class="form-control">
                <label for="edit-category">Category:</label>
                <select id="edit-category" name="category">
                    <?php
                    $categories->data_seek(0); // Reset result pointer
                    while ($row = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-control">
                <label for="edit-progress">Progress:</label>
                <input type="number" id="edit-progress" name="progress" min="0" max="100">
            </div>
            <div class="form-control">
                <input type="submit" value="Save Changes">
            </div>
        </form>
    </div>
</div>

</body>
</html>
