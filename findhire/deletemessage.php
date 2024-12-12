<?php require_once 'core/dbConfig.php'; ?>
<?php require_once 'core/models.php'; ?>
<?php  
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
}

$getUserByID = getUserByID($pdo, $_SESSION['user_id']);

if ($getUserByID['is_hr'] == 1) {
	header("Location: hr/index.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Delete a Message</title>
	<link rel="stylesheet" href="styles/styles.css">
</head>
<body>
	<?php include 'navbar.php'; ?>

    <?php  
    if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
        if ($_SESSION['status'] == "200") {
            echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
        } else {
            echo "<h1 style='color: red;'>{$_SESSION['message']}</h1>";    
        }
    }
    unset($_SESSION['message']);
    unset($_SESSION['status']);
    ?>

	<div class="delete">
		<form action="core/handleForms.php" method="POST">
			<h2 style="color: red;" >Are you sure you want to delete this?</h2>
			<?php $getMessageByID = getMessageByID($pdo, $_GET['message_id']); ?>
			<h3>Message: <?php echo $getMessageByID['description']; ?></h3>
		    <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($getMessageByID['message_id']); ?>">
		    <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($_GET['post_id'] ?? ''); ?>">
		    <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($_GET['application_id'] ?? ''); ?>">
		    <p>
		        <input type="submit" name="deleteMessageBtn" style="width: 100%;" value="Delete">
		    </p>
		</form>
	</div>
</body>
</html>