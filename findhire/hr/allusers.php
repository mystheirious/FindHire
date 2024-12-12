<?php  
require_once 'core/models.php'; 
require_once 'core/handleForms.php'; 

if (!isset($_SESSION['username'])) {
	header("Location: login.php");
}

$getUserByID = getUserByID($pdo, $_SESSION['user_id']);

if ($getUserByID['is_hr'] == 0) {
	header("Location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>All Users</title>
	<link rel="stylesheet" href="styles/styles.css">
</head>
<body>
	<?php include 'navbar.php'; ?>
	<h2>All Users</h2>
		<ul>
		    <?php 
		        $getAllUsers = getAllUsers($pdo);
		        foreach ($getAllUsers as $row) { 
		    ?>
		        <li>
		            <p><strong>Username:</strong> <?php echo htmlspecialchars($row['username']); ?></p>
		            <p><strong>First Name:</strong> <?php echo htmlspecialchars($row['first_name']); ?></p>
		            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($row['last_name']); ?></p>
		            <p><strong>Date Added:</strong> <?php echo htmlspecialchars($row['date_added']); ?></p>
		        </li>
		    <?php } ?>
		</ul>
</body>
</html>