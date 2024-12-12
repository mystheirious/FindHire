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

$message_id = $_GET['message_id'] ?? null;
$post_id = $_GET['post_id'] ?? null;
$application_id = $_GET['application_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Replies</title>
    <link rel="stylesheet" href="styles/m-styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <h1 style="text-align:center;">All Replies</h1>
    <p>Click here to <a href="send-a-message.php?post_id=<?php echo $_GET['post_id']; ?>&application_id=<?php echo $_GET['application_id']; ?>">send another message</a></p>

    <!-- Fetch all replies -->
    <div class="postContainer" style="background-color: ghostwhite; border-style: solid; border-color: gray;width: 60%; padding: 25px; margin: 0 auto;">
        <?php 
        $getAllMessages = getMessagesForPost($pdo, $_SESSION['user_id'], $post_id); 
        
        foreach ($getAllMessages as $message) {
            if ($message['message_id'] == $message_id) {  
        ?>
        <h2>Replies</h2>
        <?php 
            $getAllRepliesByMessage = getAllRepliesByMessage($pdo, $message['message_id'], $post_id, $application_id);
            
            foreach ($getAllRepliesByMessage as $row) {
        ?>
        <div class="message" style="margin-top: 20px;">
            <div class="messageContainer" style="background-color: ghostwhite; border-style: solid; border-color: gray;width: 100%; padding: 25px;">
                <h3><?php echo $row['username'];?><span style="color:red;"> (HR)</span></h3>
                <i><?php echo $row['date_added']; ?></i>
                <p><?php echo $row['description']; ?></p>
            </div>
        </div>
        <?php }  ?>
        <?php } 
        }  ?>
    </div>
</body>
</html>
