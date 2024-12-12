<?php  
require_once 'core/models.php'; 
require_once 'core/handleForms.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>HR Registration</title>
	<link rel="stylesheet" href="styles/styles.css">
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

	<form action="core/handleForms.php" method="POST">
		<h2>Register an HR on FindHire ⭑.ᐟ</h2>
		<p>
			<label for="username">Username</label>
			<input type="text" name="username">
		</p>
		<p>
			<label for="username">First Name</label>
			<input type="text" name="first_name">
		</p>
		<p>
			<label for="username">Last Name</label>
			<input type="text" name="last_name">
		</p>
		<p>
			<label for="username">Password</label>
			<input type="password" name="password">
		</p>
		<p>
			<label for="username">Confirm Password</label>
			<input type="password" name="confirm_password">
			<input type="submit" name="insertNewUserBtn" value="Register">
		</p>
	</form>
	<h2>Please don't forget to take note of the password</h2>
</body>
</html>