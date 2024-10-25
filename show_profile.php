<?php
include('includes/auth.php');
$user_id = $_SESSION['user_id'];

$_PAGETITLE = "User Profile";

// Ambil informasi user dari database
$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $created_at);
$stmt->fetch();
$stmt->close();
?>

<?php include_once('partials/header.php'); ?>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-center text-blue-600">Profile Details</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-700 font-medium">Username:</label>
                <p class="text-gray-900"><?php echo htmlspecialchars($username); ?></p>
            </div>
            <div>
                <label class="block text-gray-700 font-medium">Email:</label>
                <p class="text-gray-900"><?php echo htmlspecialchars($email); ?></p>
            </div>
            <div>
                <label class="block text-gray-700 font-medium">Account Created At:</label>
                <p class="text-gray-900"><?php echo date("F j, Y", strtotime($created_at)); ?></p>
            </div>
        </div>
        <div class="mt-6 flex flex-col sm:flex-row justify-between space-y-2 sm:space-y-0 sm:space-x-4">
            <a href="dashboard.php" class="flex-1 bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300 text-center">Back to Dashboard</a>
            <a href="edit_profile.php" class="flex-1 bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300 text-center">Edit Profile</a>
        </div>
    </div>
</body>
<?php include_once('partials/footer.php'); ?>
