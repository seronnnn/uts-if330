<?php
include('includes/auth.php');

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Mulai transaksi
    $conn->begin_transaction();
    try {
        // Update username dan email
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_username, $new_email, $user_id);

        if ($stmt->execute()) {
            // Jika password diisi, update password juga
            if ($new_password) {
                $password_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $password_stmt->bind_param("si", $new_password, $user_id);
                $password_stmt->execute();
                $password_stmt->close();
            }

            // Commit transaksi
            $conn->commit();
            $_SESSION['username'] = $new_username;
            $_SESSION['email'] = $new_email;
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

// Ambil informasi user saat ini
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Edit Profile</h2>
        <form method="post" action="" class="space-y-4">
            <div>
                <label for="username" class="block text-gray-700">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="email" class="block text-gray-700">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="password" class="block text-gray-700">New Password (leave blank to keep current password):</label>
                <input type="password" name="password" id="password" class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300">Save Changes</button>
        </form>
        <div class="text-center mt-6">
            <a href="dashboard.php" class="text-blue-500 hover:underline">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>