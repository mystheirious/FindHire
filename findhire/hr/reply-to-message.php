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
	<title>Reply to Messages</title>
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

	<!-- Fetch all messages and replies -->
	<h1>All Messages</h1>
	<div class="messageContainer">
		<?php $getAllMessages = getAllMessages($pdo, $_GET['message_id']); ?>
		<h2><?php echo $getAllMessages['username']; ?></h2>
		<i><?php echo $getAllMessages['date_added']; ?></i>
		<p><?php echo $getAllMessages['description']; ?></p>
		<hr>
		<div class="replyContainer">
			<h1>All Replies</h1>
			<?php $getAllRepliesByMessages = getAllRepliesByMessage($pdo, $_GET['message_id']);  ?>
			<?php foreach ($getAllRepliesByMessages as $row) { ?>
			<div class="reply">
				<h3><?php echo $row['username']; ?></h3>
				<i><?php echo $row['date_added']; ?></i>
				<p><?php echo $row['description']; ?></p>

				<?php if ($_SESSION['username'] == $row['username']) { ?>
					<div class="editAndDelete" style="float:right;">
						<a href="editreply.php?reply_id=<?php echo $row['reply_id'] ?>">Edit</a>
						<a href="deletereply.php?reply_id=<?php echo $row['reply_id'] ?>">Delete</a>
					</div>
				<?php } ?>

			</div>	
			<?php } ?>
			<form action="index.php" method="POST" style="margin-top: 70px;">
				<p>
					<input type="text" name="reply_description" placeholder="Reply here">
					<input type="hidden" name="message_id" value="<?php echo $_GET['message_id']; ?>">
					<input type="submit" name="insertReplyBtn" value="Reply";>
				</p>
			</form>
		</div>
	</div>
</body>
</html>