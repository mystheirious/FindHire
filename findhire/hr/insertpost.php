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
	<title>Creating Job Post</title>
	<link rel="stylesheet" href="styles/a-styles.css">
</head>
<body>
	<?php include 'navbar.php'; ?>

	<?php  
	if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

		if ($_SESSION['status'] == "200") {
			echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
		}

		else {
			echo "<h1 style='color: red;'>{$_SESSION['message']}</h1>";	
		}

	}
	unset($_SESSION['message']);
	unset($_SESSION['status']);
	?>

	<form class="job" action="core/handleForms.php" method="POST">
		<h2>Publish a job post ⭑.ᐟ</h2>
		<p>
			<label for="job_title">Job Title</label>
			<input type="text" name="job_title"></p>
		<p>
			<label for="company">Company</label>
			<input type="text" name="company">
		</p>
		<p>
			<label for="description">Job Description</label>
			<input type="text" name="description">
		</p>
		<p>
			<label for="status">Status:</label>
		    <select name="status" id="status">
		        <option value="pending">Pending</option>
		        <option value="active">Active</option>
		        <option value="closed">Closed</option>
		    </select>
		</p>
		<p>
		    <input type="submit" name="insertNewPostBtn" value="Add Post"></input>
		</p>
	</form>
</body>
</html>