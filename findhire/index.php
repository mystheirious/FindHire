<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

// Get the user by their ID
$getUserByID = getUserByID($pdo, $_SESSION['user_id']);

// Redirect if the user is an HR
if ($getUserByID['is_hr'] == 1) {
    header("Location: hr/index.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Posts</title>
    <link rel="stylesheet" href="styles/style.css">
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

    <h1 class="page-heading">Explore the latest job opportunities below ⭑.ᐟ</h1>

    <!-- Fetch all job posts -->
    <div class="job-posts-container">
        <?php 
        $getAllPosts = getAllPosts($pdo);
        foreach ($getAllPosts as $row) { 
            $application_id = checkIfApplied($pdo, $_SESSION['user_id'], $row['post_id']);
        ?>
        <div class="post-card">
            <h3 class="post-title">Job Title: <?php echo $row['job_title']; ?></h3>
            <h3 class="post-company">Company: <?php echo $row['company']; ?></h3>
            <p class="post-description">Description: <?php echo $row['description']; ?></p>
            <p class="post-status">Status: <span class="status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></p>
    
    <!-- If the job post has active status, applicants can apply -->
            <?php if ($row['status'] == 'active') { ?>
                <?php if (!$application_id): ?>
                    <form action="apply.php" method="GET" class="action-form">
                        <input type="hidden" name="post_id" value="<?php echo $row['post_id']; ?>">
                        <button type="submit" class="action-button apply">Apply</button>
                    </form>

    <!-- If an applicant already sent an application -->
                <?php else: ?>
                    <form action="viewApplication.php" method="GET" class="action-form">
                        <input type="hidden" name="post_id" value="<?php echo $row['post_id']; ?>">
                        <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                        <button type="submit" class="action-button view">View Application</button>
                    </form>
                    <form action="send-a-message.php" method="GET" class="action-form">
                        <input type="hidden" name="post_id" value="<?php echo $row['post_id']; ?>">
                        <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                        <button type="submit" class="action-button message">Send a Message</button>
                    </form>

    <!-- If the job post is closed, applicants cannot apply -->
                <?php endif; ?>
            <?php } else { ?>
                <button disabled class="action-button closed">Closed</button>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</body>
</html>
