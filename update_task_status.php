<?php
include('includes/auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $task_id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'];

    // Verifikasi bahwa task milik user yang sedang login
    $stmt = $conn->prepare("SELECT t.id FROM tasks t JOIN todo_lists tl ON t.todo_list_id = tl.id WHERE t.id = ? AND tl.user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        // Update status task ke "completed"
        $update_stmt = $conn->prepare("UPDATE tasks SET is_completed = 1 WHERE id = ?");
        $update_stmt->bind_param("i", $task_id);
        $update_stmt->execute();
        $update_stmt->close();
        echo "success";
    } else {
        echo "error";
    }
}
?>