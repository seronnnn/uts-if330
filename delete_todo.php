<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

include('includes/auth.php');

if (isset($_GET['id'])) {
    $todo_list_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id FROM todo_lists WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("ii", $todo_list_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        $delete_tasks_stmt = $conn->prepare("DELETE FROM tasks WHERE todo_list_id = ?");
        if (!$delete_tasks_stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $delete_tasks_stmt->bind_param("i", $todo_list_id);
        $delete_tasks_stmt->execute();
        $delete_tasks_stmt->close();

        $delete_stmt = $conn->prepare("DELETE FROM todo_lists WHERE id = ?");
        if (!$delete_stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $delete_stmt->bind_param("i", $todo_list_id);

        if ($delete_stmt->execute()) {
            $delete_stmt->close();
            header("Location: dashboard.php?message=deleted");
            exit();
        } else {
            $delete_stmt->close();
            header("Location: dashboard.php?error=deletefailed");
            exit();
        }
    } else {
        $stmt->close();
        header("Location: dashboard.php?error=notfound");
        exit();
    }
} else {
    header("Location: dashboard.php?error=noid");
    exit();
}