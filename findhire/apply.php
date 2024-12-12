<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : null;
if (!$post_id) {
    echo "Post ID is missing!";
    exit();
}

$postDetails = getPostByID($pdo, $post_id);
$getUserByID = getUserByID($pdo, $_SESSION['user_id']);

if ($getUserByID['is_hr'] == 1) {
    header("Location: hr/index.php");
}

if ($postDetails['status'] == 'closed') {
    $_SESSION['message'] = "Sorry, this job post is closed.";
    $_SESSION['status'] = "400";
    header("Location: index.php");
    exit;
}

$existingApplication = checkIfApplied($pdo, $_SESSION['user_id'], $post_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
    <link rel="stylesheet" href="styles/a-styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <?php  
    if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
        echo "<div class='message-container'>";
        if ($_SESSION['status'] == "200") {
            echo "<p class='message success'>{$_SESSION['message']}</p>";
        } else {
            echo "<p class='message error'>{$_SESSION['message']}</p>"; 
        }
        echo "</div>";
        unset($_SESSION['message']);
        unset($_SESSION['status']);
    }
    ?>
    
    <h1 class="page-heading">Would you like to apply for <?php echo htmlspecialchars($postDetails['job_title']); ?> at <?php echo htmlspecialchars($postDetails['company']); ?>?</h1>

    <!-- If an applicant already applied -->
    <?php if ($existingApplication): ?>
        <div class="action-container">
            <form action="viewApplication.php" method="GET" class="action-form">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <input type="hidden" name="application_id" value="<?php echo $existingApplication['application_id']; ?>">
                <button type="submit" class="action-button view">View Application</button>
            </form>
            <form action="send-a-message.php" method="GET" class="action-form">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <input type="hidden" name="application_id" value="<?php echo $existingApplication['application_id']; ?>">
                <button type="submit" class="action-button message">Send a Message</button>
            </form>
        </div>
    <?php else: ?>

    <!-- Application Form -->
        <div class="application-review">
            <form action="core/handleForms.php" method="POST" enctype="multipart/form-data" class="application-form">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <textarea name="reason" rows="5" cols="40" placeholder="Why are you the best fit for this position?" required class="form-textarea"></textarea><br><br>
                <label for="pdf" class="form-label">Upload your resume (PDF only):</label>
                <input type="file" name="pdf" accept="application/pdf" required class="form-input"><br><br>
                <input type="hidden" name="status" value="pending">
                <input type="submit" name="submitApplicationBtn" value="Submit Application" class="action-button submit">
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
