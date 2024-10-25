<?php
include('includes/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);
    
    if ($stmt->execute()) {
        $success = "Registration successful. <a href='login.php' class='text-blue-500 hover:underline'>Login here</a>";
    } else {
        $error = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}
?>

<?php include_once('partials/header.php'); ?>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-pink-500 to-yellow-500 p-4">
    <div class="bg-white bg-opacity-10 p-6 sm:p-10 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl sm:text-3xl font-bold text-white mb-6 text-center">Register</h2>
        <?php 
        if (isset($error)) { echo "<p class='text-red-500 mb-4 text-center'>$error</p>"; }
        if (isset($success)) { echo "<p class='text-green-500 mb-4 text-center'>$success</p>"; }
        ?>
        <form method="post" action="" class="space-y-4">
            <div>
                <label for="username" class="block text-white">Username:</label>
                <input type="text" name="username" id="username" required class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label for="email" class="block text-white">Email:</label>
                <input type="email" name="email" id="email" required class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label for="password" class="block text-white">Password:</label>
                <input type="password" name="password" id="password" required class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            <button type="submit" class="w-full bg-yellow-500 text-white py-2 rounded-md hover:bg-yellow-600 transition duration-300">Register</button>
        </form>
        <p class="mt-6 text-center text-white">
            Already have an account? <a href="login.php" class="text-yellow-300 hover:text-yellow-400 font-semibold">Login here</a>
        </p>
    </div>
</body>
<?php include_once('partials/footer.php'); ?>
