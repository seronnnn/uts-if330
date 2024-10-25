<?php
include('includes/auth.php');
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

$_PAGETITLE = "Dashboard";

$sort_order = $_GET['sort'] ?? 'asc';
$search_keyword = $_GET['search'] ?? '';

$stmt_user = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$stmt_user->bind_result($username, $email);
$stmt_user->fetch();
$stmt_user->close();

$query = "SELECT id, title FROM todo_lists WHERE user_id = ?";

if (!empty($search_keyword)) {
    $query .= " AND title LIKE ?";
}

$query .= " ORDER BY title " . ($sort_order === 'desc' ? 'DESC' : 'ASC');

$stmt = $conn->prepare($query);

if (!empty($search_keyword)) {
    $search_param = '%' . $search_keyword . '%';
    $stmt->bind_param("is", $user_id, $search_param);
} else {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<?php include_once('partials/header.php'); ?>
<body class="bg-gradient-to-r from-blue-500 to-indigo-500 min-h-screen flex items-center justify-center p-4">
    <div id="content" class="bg-white p-6 sm:p-8 rounded-lg shadow-lg w-full max-w-2xl flex flex-col justify-between h-auto">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-indigo-600">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p class="text-gray-500"><?php echo htmlspecialchars($email); ?></p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-4">
                <a href="show_profile.php"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">View
                    Profile</a>
                <a href="logout.php"
                    class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition duration-300">Logout</a>
            </div>
        </div>

        <form method="get" action="" class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0">
            <input type="text" name="search" placeholder="Search to-do lists..."
                value="<?php echo htmlspecialchars($search_keyword); ?>"
                class="w-full sm:w-2/3 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 mr-2">
            <div class="w-full sm:w-auto flex space-x-2">
                <select name="sort"
                    class="w-full sm:w-auto px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="asc" <?php echo $sort_order === 'asc' ? 'selected' : ''; ?>>A-Z</option>
                    <option value="desc" <?php echo $sort_order === 'desc' ? 'selected' : ''; ?>>Z-A</option>
                </select>
                <button type="submit"
                    class="w-full sm:w-auto bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">Apply</button>
            </div>
        </form>

        <ul class="todo-list space-y-4 mb-6 flex-grow">
            <?php if ($result->num_rows > 0) { ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <li class="flex justify-between items-center bg-gray-100 p-4 rounded-md border border-gray-200 hover:bg-gray-200 transition duration-300 cursor-pointer"
                        onclick="window.location.href='view_todo.php?id=<?php echo $row['id']; ?>'">
                        <span class="flex-grow tex-blue-600 hover:text-blue-700 font-semibold">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </span>
                        <a href="delete_todo.php?id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700 font-medium ml-4">Delete</a>
                    </li>
                <?php } ?>
            <?php } else { ?>
                <li class="text-gray-500 text-center p-4">No to-do lists found. Create your first one!</li>
            <?php } ?>
        </ul>

        <div class="text-center mt-auto">
            <a href="create_todo.php"
                class="inline-block px-4 py-3 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-300">Create
                New To-Do List</a>
        </div>
    </div>
    <style>
        #content {
            max-width: 800px;
            width: 100%;
            height: auto; /* Changed to auto to adjust based on content */
            min-height: 500px;
            overflow-y: auto;
        }
        @media (max-width: 640px) {
            .flex-col {
                flex-direction: column;
            }
            .w-full {
                width: 100%;
            }
        }
    </style>
</body>
<?php include_once('partials/footer.php'); ?>
