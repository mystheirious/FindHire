<?php require_once 'core/dbConfig.php'; ?>
<?php require_once 'core/models.php'; ?>
<?php require_once 'core/handleForms.php'; ?>

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
    <title>Send a Message</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <h1 style="text-align:center;">Send a message to the HR representative ⭑.ᐟ</h1>
        <p>Click here to view <a style="margin-bottom: 20px;" href="messages.php?post_id=<?php echo $_GET['post_id']; ?>&application_id=<?php echo $_GET['application_id']; ?>">all messages</a></p>
        <div class="formContainer" style="display: flex; width: 480px; margin-left: 30px;">
            <form action="core/handleForms.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
                <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($application_id); ?>">

                <!-- Form for sending a message -->
                <p>
                    <label for="message_description">Content</label>
                    <input type="text" name="message_description" required>
                    <input type="submit" name="insertMessageBtn" value="Send" style="margin-top: 25px;">
                </p>
            </form>    
        </div>
    <p>
</body>
</html>
