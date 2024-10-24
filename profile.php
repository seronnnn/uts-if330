<?php
include('includes/auth.php');

$user_id = $_SESSION['user_id'];
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
    <title>User Profile</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h2>User Profile</h2>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <a href="update_profile.php">Edit Profile</a>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>