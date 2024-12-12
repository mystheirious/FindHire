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

$post_id = $_GET['post_id'] ?? null;
$application_id = $_GET['application_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Edit a Message</title>
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
	
	<h1 style="text-align:center;">Edit your message ⭑.ᐟ</h1>
	<div class="formContainer" style="display: flex; justify-content: center; width: 480px;">
		<?php $getMessageByID = getMessageByID($pdo, $_GET['message_id']); ?>
		<form action="core/handleForms.php" method="POST">
		    <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($_GET['message_id']); ?>"> 
		    <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
		    <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($application_id); ?>">
		    <label for="message_description">Content</label>
		    <input type="text" name="message_description" value="<?php echo htmlspecialchars($getMessageByID['description']); ?>">
		    <input type="submit" name="editMessageBtn" value="Save">
		</form>
	</div>
	
</body>
</html>