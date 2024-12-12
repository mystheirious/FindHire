<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';
require_once 'core/handleForms.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$getUserByID = getUserByID($pdo, $_SESSION['user_id']);

if ($getUserByID['is_hr'] == 1) {
    header("Location: hr/index.php");
    exit();
}


$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : null;
if (!$post_id) {
    echo "Post ID is missing!";
    exit();
}

$application_id = $_GET['application_id'] ?? null;  // Retrieve application_id from the URL
if (!$application_id) {
    echo "Application ID is missing!";
    exit();
}

$application = getApplicationById($pdo, $post_id, $_SESSION['user_id']);
$job = getPostByID($pdo, $post_id);

if (!$application) {
    echo "Application not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Your Application</title>
    <link rel="stylesheet" href="styles/a-styles.css">
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

    <div class="application-review">
        <h1>View your application for <?php echo htmlspecialchars($job['job_title']); ?> ⭑.ᐟ</h1>

        <!-- Application details -->
        <div class="application-detail-box">
            <h3>Reason for Application</h3>
            <p><?php echo !empty($application['reason']) ? htmlspecialchars($application['reason']) : 'N/A'; ?></p>
        </div>

        <div class="application-detail-box">
            <h3>Resume</h3>
            <p>
                <?php 
                $pdfPath = $application['pdf_path'];
                if (!empty($pdfPath)) {
                    if (strpos($pdfPath, 'uploads/') === 0) {
                        $pdfPath = substr($pdfPath, strlen('uploads/'));
                    }
                    echo '<a href="uploads/' . htmlspecialchars($pdfPath) . '" target="_blank">View Document</a>';
                } else {
                    echo 'No document submitted.';
                }
                ?>
            </p>
        </div>

        <div class="application-detail-box">
            <h3>Application Status</h3>
            <p><?php echo ucfirst(htmlspecialchars($application['status'])); ?></p>
        </div>
    </div>

    <div class="application-review">
        <?php if ($application['status'] == 'pending'): ?>

            <!-- Form for updating application -->
            <div class="application-update-form">
                <h3>Update your application</h3>
                <form action="viewApplication.php?post_id=<?php echo $post_id; ?>&application_id=<?php echo $application['application_id']; ?>" method="POST" enctype="multipart/form-data">
                    <label for="reason">Why are you the best fit for this position?</label><br>
                    <textarea name="reason" rows="4" cols="50"><?php echo htmlspecialchars($application['reason']); ?></textarea><br><br>

                    <label for="pdf">Upload your resume (PDF only):</label><br>
                    <input type="file" name="pdf" id="pdf"><br><br>

                    <input type="submit" name="updateApplicationBtn" value="Update Application">
                </form>
            </div>

            <!-- Form for withdrawing the application -->
            <div class="application-withdraw-form">
                <form action="viewApplication.php?post_id=<?php echo $post_id; ?>&application_id=<?php echo $application['application_id']; ?>" method="POST">
                    <input type="submit" name="withdrawApplicationBtn" value="Withdraw Application">
                </form>
            </div>

        <!-- Display acceptance and rejection message -->
        <?php elseif ($application['status'] == 'accepted'): ?>
            <div class="application-status-message">
                <p>Congratulations! You have been accepted for this position.</p>
            </div>
        <?php elseif ($application['status'] == 'rejected'): ?>
            <div class="application-status-message">
                <p>Sorry, you have not been accepted for this position.</p>
            </div>
        <?php endif; ?>
        </div>
</body>
</html>
