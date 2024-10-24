<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/auth.php');

if (isset($_GET['id'])) {
    $todo_list_id = intval($_GET['id']); // Mengambil ID to-do list dari URL dan mengonversinya ke integer

    // Verifikasi bahwa to-do list ini milik user yang sedang login
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id FROM todo_lists WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("ii", $todo_list_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    // Jika to-do list ditemukan dan milik user ini
    if ($stmt->num_rows > 0) {
        $stmt->close();
        
        // Hapus semua tasks yang terkait dengan to-do list ini
        $delete_tasks_stmt = $conn->prepare("DELETE FROM tasks WHERE todo_list_id = ?");
        if (!$delete_tasks_stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $delete_tasks_stmt->bind_param("i", $todo_list_id);
        $delete_tasks_stmt->execute();
        $delete_tasks_stmt->close();
        
        // Menghapus to-do list dari database
        $delete_stmt = $conn->prepare("DELETE FROM todo_lists WHERE id = ?");
        if (!$delete_stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $delete_stmt->bind_param("i", $todo_list_id);

        if ($delete_stmt->execute()) {
            $delete_stmt->close();
            // Redirect ke dashboard dengan pesan sukses
            header("Location: dashboard.php?message=deleted");
            exit();
        } else {
            // Tangani error dan redirect dengan pesan error
            $delete_stmt->close();
            header("Location: dashboard.php?error=deletefailed");
            exit();
        }
    } else {
        // Jika to-do list tidak ditemukan atau bukan milik user ini, redirect dengan pesan error
        $stmt->close();
        header("Location: dashboard.php?error=notfound");
        exit();
    }
} else {
    // Jika tidak ada ID to-do list di URL, redirect ke dashboard dengan pesan error
    header("Location: dashboard.php?error=noid");
    exit();
}
?>