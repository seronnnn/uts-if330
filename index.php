<?php
session_start();
include('includes/config.php');

$_PAGETITLE = "Welcome";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<?php include_once('partials/header.php'); ?>
<body class="flex items-center justify-center h-screen bg-gradient-to-r from-blue-500 to-purple-600">
    <div class="bg-white bg-opacity-10 p-10 rounded-lg shadow-lg text-center max-w-md w-full">
        <h1 class="text-4xl font-bold text-white mb-6">Welcome to the Online To-Do List</h1>
        <p class="text-white mb-8">Organize your tasks efficiently and stay productive with our simple to-do list app.</p>
        <div class="flex justify-around">
            <a href="login.php" class="px-6 py-2 bg-white text-blue-500 font-semibold rounded-md shadow-md hover:bg-blue-500 hover:text-white transition duration-300">Login</a>
            <a href="register.php" class="px-6 py-2 bg-white text-blue-500 font-semibold rounded-md shadow-md hover:bg-blue-500 hover:text-white transition duration-300">Sign Up</a>
        </div>
    </div>
</body>
<?php include_once('partials/footer.php'); ?>