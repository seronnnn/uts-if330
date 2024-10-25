<?php
include_once('includes/config.php'); 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email']; 

    // Cek email ada di database
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika email ditemukan, ambil user_id
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        header('Location: reset_password.php?user_id=' . $user_id);
        exit();

    } else {
        $error = "Email tidak terdaftar.";
    }
}
// cek kalo methodnya post
// jika post maka
    // ambil email dari form 
    // cek apakah email ada di database
    // jika ada maka
        // ambil user_id dari database
        // kirim email ke user_id
        // redirect ke halaman reset_password.php dengan query string user_id
    // jika tidak maka
        // tampilkan pesan error

// header('location: reset_password.php?user_id=' . $user_id); ini clue nya 

?>

<?php include_once('partials/header.php'); ?>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-green-400 to-blue-500 p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Reset Password</h2>
        <?php if (isset($error)) { echo "<p class='text-red-500 mb-4 text-center'>$error</p>"; } ?>
        <?php if (isset($success)) { echo "<p class='text-green-500 mb-4 text-center'>$success</p>"; } ?>
        
        <form method="post" action="forgot_password.php" 
            class="space-y-4">
            <div>
                <label for="email" class="block text-gray-700">Email:</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit"
                class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300">
                Reset Password
            </button>
        </form>
    </div>
</body>
<?php include_once('partials/footer.php'); ?>
