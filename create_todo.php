<?php
include('includes/auth.php');

$_PAGETITLE = "Tambah Todo Baru";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $user_id = $_SESSION['user_id'];
    
    // Mulai transaksi
    $conn->begin_transaction();
    try {
        // Buat to-do list baru
        $stmt = $conn->prepare("INSERT INTO todo_lists (user_id, title) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $title);
        
        if ($stmt->execute()) {
            // Jika semua berhasil, commit transaksi
            $conn->commit();
            header("Location: dashboard.php");
            exit();
        } else {
            throw new Exception("Error: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        $conn->rollback();
        echo $e->getMessage();
    }

    $stmt->close();
}
?>

<?php include_once('partials/header.php'); ?>
<body class="flex items-center justify-center h-screen bg-gradient-to-r from-green-400 to-blue-500">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Create New To-Do List</h2>
        <form method="post" action="" class="space-y-4">
            <div>
                <label for="title" class="block text-gray-700">Title:</label>
                <input type="text" name="title" id="title" required class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300">Create</button>
        </form>
        <div class="text-center mt-6">
            <a href="dashboard.php" class="text-blue-500 hover:underline">Back to Dashboard</a>
        </div>
    </div>
</body>
<?php include_once('partials/footer.php'); ?>