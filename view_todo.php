<?php
include('includes/auth.php');

$todo_list_id = $_GET['id'];
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';

$stmt = $conn->prepare("SELECT title FROM todo_lists WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $todo_list_id, $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();

// Query dasar untuk mengambil tasks berdasarkan filter dan pencarian
$query = "SELECT id, description, is_completed, due_date FROM tasks WHERE todo_list_id = ?";

if ($status_filter === 'completed') {
    $query .= " AND is_completed = 1";
} elseif ($status_filter === 'incomplete') {
    $query .= " AND is_completed = 0";
}

if (!empty($search_keyword)) {
    $query .= " AND description LIKE ?";
}

$stmt = $conn->prepare($query);

if (!empty($search_keyword)) {
    $search_param = '%' . $search_keyword . '%';
    $stmt->bind_param("is", $todo_list_id, $search_param);
} else {
    $stmt->bind_param("i", $todo_list_id);
}

$stmt->execute();
$tasks = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 12px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        #content {
            max-width: 800px;
            height: 90vh;
            min-height: 500px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-blue-500 min-h-screen flex items-center justify-center">
    <div id="content" class="bg-white p-8 rounded-lg shadow-lg w-full flex flex-col justify-between">
        <h2 class="text-3xl font-bold mb-6 text-center text-indigo-700"><?php echo htmlspecialchars($title); ?></h2>
        <div class="text-center mb-6">
            <a href="create_task.php?todo_list_id=<?php echo $todo_list_id; ?>" class="inline-block px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-300">
                <i class="fas fa-plus-circle"></i> Add New Task
            </a>
        </div>
        <form method="get" action="" class="mb-6 flex justify-between items-center">
            <input type="hidden" name="id" value="<?php echo $todo_list_id; ?>">
            <input type="text" name="search" placeholder="Search tasks..." value="<?php echo htmlspecialchars($search_keyword); ?>" class="w-2/3 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 mr-2">
            <select name="status" class="px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 mr-2">
                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Tasks</option>
                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed Tasks</option>
                <option value="incomplete" <?php echo $status_filter === 'incomplete' ? 'selected' : ''; ?>>Incomplete Tasks</option>
            </select>
            <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition duration-300">Filter</button>
        </form>
        <ul class="space-y-4 flex-grow">
            <?php while ($task = $tasks->fetch_assoc()) { ?>
                <li class="flex justify-between items-center bg-gray-50 p-4 rounded-md shadow-sm hover:bg-indigo-100 transition duration-300 cursor-pointer" onclick="openModal(<?php echo $task['id']; ?>, '<?php echo addslashes($task['description']); ?>', '<?php echo $task['is_completed']; ?>')">
                    <span class="flex items-center flex-grow <?php echo $task['is_completed'] ? 'line-through text-gray-500' : 'text-gray-800'; ?>">
                        <i class="<?php echo $task['is_completed'] ? 'fas fa-check-circle text-green-500 mr-2' : 'fas fa-circle text-gray-400 mr-2'; ?>"></i>
                        <?php echo htmlspecialchars($task['description']); ?>
                    </span>
                    <span class="text-gray-500 text-sm">
                        <?php 
                        if (!empty($task['due_date'])) {
                            echo "Due: " . date("F j, Y", strtotime($task['due_date']));
                        } else {
                            echo "No due date";
                        }
                        ?>
                    </span>
                </li>
            <?php } ?>
        </ul>
        <div class="text-center mt-8">
            <a href="dashboard.php" class="inline-block px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 transition duration-300">Back to Dashboard</a>
        </div>
    </div>

    <div id="taskModal" class="modal flex">
        <div class="modal-content">
            <h3 class="text-lg font-bold mb-4" id="taskDescription"></h3>
            <div class="flex justify-between">
                <button id="completeButton" class=" bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-300">Mark as Completed</button>
                <button onclick="closeModal()" class=" bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-300">Close</button>
            </div>
        </div>
    </div>

    <script>
        function openModal(taskId, taskDescription, isCompleted) {
            const modal = document.getElementById('taskModal');
            const descriptionElement = document.getElementById('taskDescription');
            const completeButton = document.getElementById('completeButton');

            descriptionElement.textContent = taskDescription;

            if (isCompleted == 1) {
                completeButton.disabled = true;
                completeButton.textContent = "Already Completed";
                completeButton.classList.add('bg-gray-500', 'hover:bg-gray-600');
                completeButton.classList.remove('bg-green-500', 'hover:bg-green-600');
            } else {
                completeButton.disabled = false;
                completeButton.textContent = "Mark as Completed";
                completeButton.classList.remove('bg-gray-500', 'hover:bg-gray-600');
                completeButton.classList.add('bg-green-500', 'hover:bg-green-600');
                completeButton.onclick = function () {
                    markAsCompleted(taskId);
                };
            }

            modal.style.display = 'flex';
        }

        function closeModal() {
            const modal = document.getElementById('taskModal');
            modal.style.display = 'none';
        }

        function markAsCompleted(taskId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_task_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send("id=" + taskId);
        }
    </script>
</body>
</html>