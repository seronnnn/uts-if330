<?php
include('includes/auth.php'); // Pastikan user sudah login

// Verifikasi bahwa ada ID task yang dikirimkan melalui URL
if (isset($_GET['id'])) {
    $task_id = intval($_GET['id']); // Mengambil ID task dari URL dan mengonversinya ke integer

    // Verifikasi bahwa task ini milik user yang sedang login
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
    
    // Jika task ditemukan dan milik user ini
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($task_id, $todo_list_id);
        $stmt->fetch();
        $stmt->close();

        // Menghapus task dari database
        $delete_stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $delete_stmt->bind_param("i", $task_id);

        if ($delete_stmt->execute()) {
            // Redirect ke halaman detail to-do list setelah berhasil menghapus task
            header("Location: view_todo.php?id=" . $todo_list_id);
            exit();
        } else {
            // Menampilkan pesan error jika terjadi kesalahan saat menghapus
            echo "Error: " . $delete_stmt->error;
        }

        $delete_stmt->close();
    } else {
        // Jika task tidak ditemukan atau bukan milik user ini, redirect ke dashboard
        header("Location: dashboard.php");
        exit();
    }
} else {
    // Jika tidak ada ID task di URL, redirect ke dashboard
    header("Location: dashboard.php");
    exit();
}
?>