<?php
include('includes/auth.php');

$user_id = $_SESSION['user_id'];

// Ambil informasi user dari database
$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $created_at);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Profile Details</h2>
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
        <div class="text-center mt-6 flex justify-between">
            <a href="dashboard.php" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 shadow-md p-2 rounded-md m-2">Back to Dashboard</a>
            <a href="edit_profile.php" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 shadow-md p-2 rounded-md m-2">Edit Profile</a>
        </div>
    </div>
</body>
</html>