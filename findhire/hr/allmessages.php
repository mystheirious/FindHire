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

$post_id = $_GET['post_id'] ?? null;
$application_id = $_GET['application_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Messages</title>
    <link rel="stylesheet" href="styles/m-styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <h1>Messages from the applicant</h1>

    <!-- Fetch messages related to the specific application -->
    <?php
    if ($application_id) {
        $getMessagesForApplicant = getMessagesForApplication($pdo, $application_id);
        foreach ($getMessagesForApplicant as $message) {
    ?>
        <div class="messageContainer">
            <h2><?php echo htmlspecialchars($message['username']); ?></h2>
            <i><?php echo htmlspecialchars($message['date_added']); ?></i>
            <p><?php echo htmlspecialchars($message['description']); ?></p>
            <a href="reply-to-message.php?message_id=<?php echo $message['message_id']; ?>" style="float: right;">Reply</a>
        </div>
    <?php
        }
    } else {
        echo "<p>No messages found for this applicant.</p>";
    }
    ?>

</body>
</html>
