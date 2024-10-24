<?php
include('includes/auth.php');

if (isset($_GET['id'])) {
    $task_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("
        SELECT t.id, t.todo_list_id 
        FROM tasks t 
        JOIN todo_lists tl ON t.todo_list_id = tl.id 
        WHERE t.id = ? AND tl.user_id = ?
    ");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($task_id, $todo_list_id);
        $stmt->fetch();
        $stmt->close();

        $delete_stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $delete_stmt->bind_param("i", $task_id);

        if ($delete_stmt->execute()) {
            header("Location: view_todo.php?id=" . $todo_list_id);
            exit();
        } else {
            echo "Error: " . $delete_stmt->error;
        }

        $delete_stmt->close();
    } else {
        header("Location: dashboard.php");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}