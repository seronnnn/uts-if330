<?php
include_once('includes/config.php'); // Pastikan koneksi database

// Cek apakah user_id ada di URL
if (!isset($_GET['user_id'])) {
    die("User ID tidak tersedia.");
}

$user_id = $_GET['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        // Enkripsi password baru
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Update password di database
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            echo "<script>window.onload = function() { showPopup('Password berhasil direset. Silakan <a href=\'login.php\'>login</a>.', 'success'); }</script>";
        } else {
            echo "<script>window.onload = function() { showPopup('Kesalahan saat memperbarui password.', 'error'); }</script>";
        }
    } else {
        echo "<script>window.onload = function() { showPopup('Password tidak cocok.', 'error'); }</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"> 
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: white;
            border: 2px solid white;
            border-radius: 8px;
            padding: 10px;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .popup.success {
            background-color: #DFF2BF;
            color: #4F8A10;
        }
        .popup.error {
            background-color: #FFBABA;
            color: #D8000C;
        }
        .login-button {
            text-align: center;
            display: block;
            color: #1D4ED8; 
            margin-top: 15px;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        .login-button:hover {
            color: #1E40AF; 
        }
    </style>
    <script>
        function showPopup(message, type) {
            const popup = document.getElementById('popup');
            popup.innerHTML = message;
            popup.className = `popup ${type}`;
            popup.style.display = 'block';
            setTimeout(() => {
                popup.style.display = 'none';
            }, 5000); 
        }
        function showLoginButton() {
            const loginButton = document.getElementById('loginButton');
            loginButton.style.display = 'block';
        }
    </script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-green-400 to-blue-500">
    <div class="popup" id="popup"></div> 
    <div class="bg-white bg-opacity-90 p-8 rounded-lg shadow-lg w-full max-w-md mx-4">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Reset Password</h2>
        <form method="post" action="" class="space-y-4">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>"> 
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>"> 
            <div>
                <label for="new_password" class="block text-gray-700">New Password:</label>
                <input type="password" name="new_password" id="new_password" required class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="confirm_password" class="block text-gray-700">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300">Reset Password</button>
        </form>

        <a href="login.php" id="loginButton" class="login-button">Login</a> 
    </div>
</body>
</html>