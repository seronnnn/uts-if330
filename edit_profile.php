<?php
include('includes/auth.php');
$user_id = $_SESSION['user_id'];

$_PAGETITLE = "Dasboard";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_username, $new_email, $user_id);

        if ($stmt->execute()) {
            if ($new_password) {
                $password_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $password_stmt->bind_param("si", $new_password, $user_id);
                $password_stmt->execute();
                $password_stmt->close();
            }

            $conn->commit();
            $_SESSION['username'] = $new_username;
            $_SESSION['email'] = $new_email;
            header("Location: dashboard.php");
            exit();
        } else {
            throw new Exception("Error: " . $stmt->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }

    $stmt->close();
}

$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();
?>

<?php include_once('partials/header.php'); ?>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 sm:p-8">
    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-center text-blue-600">Edit Profile</h2>
        <form method="post" action="" class="space-y-4">
            <div>
                <label for="username" class="block text-gray-700">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>"
                    required
                    class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="email" class="block text-gray-700">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required
                    class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="password" class="block text-gray-700">New Password (leave blank to keep current
                    password):</label>
                <input type="password" name="password" id="password"
                    class="w-full px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit"
                class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300">Save
                Changes</button>
        </form>
        <div class="text-center mt-6">
            <a href="dashboard.php" class="block w-full text-blue-500 hover:underline text-center py-2">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
<?php include_once('partials/footer.php'); ?>
