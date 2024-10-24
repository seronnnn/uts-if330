<?php
// session_start();
include('includes/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();
        
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "No user found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center h-screen bg-gradient-to-r from-green-400 to-blue-500">
    <div class="bg-white bg-opacity-10 p-10 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-3xl font-bold text-white mb-6 text-center">Login</h2>
        <?php if (isset($error)) { echo "<p class='text-red-500 mb-4 text-center'>$error</p>"; } ?>
        <form method="post" action="" class="space-y-4">
            <div>
                <label for="email" class="block text-white">Email:</label>
                <input type="email" name="email" id="email" required class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="password" class="block text-white">Password:</label>
                <input type="password" name="password" id="password" required class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300">Login</button>
        </form>
        <p class="text-white mt-6 text-center">Don't have an account? <a href="register.php" class="text-blue-200 hover:underline">Sign up here</a>.</p>
    </div>
</body>
</html>
