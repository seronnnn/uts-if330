<?php
include('includes/auth.php');

$_PAGETITLE = "View Todo List";

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

<?php include_once('partials/header.php'); ?>
<body class="bg-gradient-to-r from-indigo-300 to-blue-500 min-h-screen flex items-center justify-center p-4">
    <div id="content" class="bg-white p-6 sm:p-8 rounded-lg shadow-lg w-full max-w-2xl flex flex-col justify-between">
        <h2 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6 text-center text-indigo-700"><?php echo htmlspecialchars($title); ?></h2>
        <div class="text-center mb-6">
            <a href="create_task.php?todo_list_id=<?php echo $todo_list_id; ?>" class="inline-block px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-300">
                <i class="fas fa-plus-circle"></i> Add New Task
            </a>
        </div>
        <form method="get" action="" class="mb-6 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
            <input type="hidden" name="id" value="<?php echo $todo_list_id; ?>">
            <input type="text" name="search" placeholder="Search tasks..." 
                value="<?php echo htmlspecialchars($search_keyword); ?>" 
                class="w-full sm:w-2/3 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <select name="status" class="w-full sm:w-auto px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Tasks</option>
                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed Tasks</option>
                <option value="on going" <?php echo $status_filter === 'incomplete' ? 'selected' : ''; ?>>on going Tasks</option>
            </select>
            <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition duration-300">Filter</button>
        </form>
        <ul class="space-y-4 flex-grow">
        <ul class="space-y-4 flex-grow">
    <?php while ($task = $tasks->fetch_assoc()) { ?>
        <li class="flex flex-col sm:flex-row justify-between items-center bg-gray-50 p-4 rounded-md border border-gray-200 hover:bg-indigo-100 transition duration-300 cursor-pointer" 
            onclick="openModal(<?php echo $task['id']; ?>, '<?php echo addslashes($task['description']); ?>', '<?php echo $task['is_completed']; ?>')">
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
                    <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="text-red-500 hover:text-red-700 font-medium ml-4" 
                        onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this task?');">Delete</a>
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

            completeButton.disabled = false;
            completeButton.classList.remove('bg-gray-500', 'hover:bg-gray-600', 'bg-green-500', 'hover:bg-green-600', 'bg-yellow-500', 'hover:bg-yellow-600');

            if (isCompleted == 0) {
                completeButton.textContent = "Mark as Completed";
                completeButton.classList.add('bg-green-500', 'hover:bg-green-600');
                completeButton.onclick = function() {
                    markAsCompleted(taskId);  
            };
            } else {
                completeButton.textContent = "Mark as On Going";
                completeButton.classList.add('bg-red-500', 'hover:bg-red-600');
                completeButton.onclick = function() {
                    markAsOngoing(taskId); 
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
            xhr.send("id=" + taskId + "&status=1"); 
        }

        function markAsIncomplete(taskId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_task_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send("id=" + taskId + "&status=0"); 
        }
        function markAsOngoing(taskId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_task_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send("id=" + taskId + "&status=0"); 
        }
    </script>
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
            padding: 16px;
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
</body>
<?php include_once('partials/footer.php'); ?>