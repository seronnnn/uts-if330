<?php
include('includes/auth.php'); // Pastikan user sudah login

if (isset($_GET['todo_list_id'])) {
    $todo_list_id = intval($_GET['todo_list_id']); // Ambil ID to-do list dari URL

    // Verifikasi bahwa to-do list ini milik user yang sedang login
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id FROM todo_lists WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $todo_list_id, $user_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        // Jika to-do list tidak ditemukan atau bukan milik user ini, redirect ke dashboard
        header("Location: dashboard.php");
        exit();
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $task_description = $_POST['task_description'];
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null; // Ambil due date jika ada
        
        $task_stmt = $conn->prepare("INSERT INTO tasks (todo_list_id, description, due_date) VALUES (?, ?, ?)");
        $task_stmt->bind_param("iss", $todo_list_id, $task_description, $due_date);
        
        if ($task_stmt->execute()) {
            header("Location: view_todo.php?id=" . $todo_list_id);
            exit();
        } else {
            $error = "Error: " . $task_stmt->error;
        }

        $task_stmt->close();
    }
} else {
    // Jika tidak ada todo_list_id di URL, redirect ke dashboard
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Task</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center h-screen bg-gradient-to-r from-green-400 to-blue-500">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Add New Task</h2>
        <?php if (isset($error)) { echo "<p class='text-red-500 mb-4'>$error</p>"; } ?>
        <form method="post" action="" class="space-y-4">
            <div>
                <label for="task_description" class="block text-gray-700">Task Description:</label>
                <input type="text" name="task_description" id="task_description" required class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="due_date" class="block text-gray-700">Due Date (optional):</label>
                <input type="date" name="due_date" id="due_date" class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300">Add Task</button>
        </form>
        <div class="text-center mt-6">
            <a href="view_todo.php?id=<?php echo $todo_list_id; ?>" class="text-blue-500 hover:underline">Back to To-Do List</a>
        </div>
    </div>
</body>
</html>