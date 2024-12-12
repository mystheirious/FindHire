<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$getUserByID = getUserByID($pdo, $_SESSION['user_id']);
if ($getUserByID['is_hr'] == 0) {
    header("Location: ../index.php");
    exit();
}

$post_id = $_GET['post_id'];
$job = getPostByID($pdo, $post_id);

// Fetch applications with JOIN to get username
$query = "
    SELECT 
        applications.application_id, 
        CONCAT(user_accounts.first_name, ' ', user_accounts.last_name) AS name,
        applications.reason, 
        applications.status, 
        applications.pdf_path
    FROM 
        applications
    JOIN 
        user_accounts 
    ON 
        applications.user_id = user_accounts.user_id
    WHERE 
        applications.post_id = :post_id
";
$stmt = $pdo->prepare($query);
$stmt->execute(['post_id' => $post_id]);

// Handle the button click
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acceptBtn']) || isset($_POST['rejectBtn'])) {
        $application_id = $_POST['application_id'];

        if (isset($_POST['acceptBtn'])) {
            $status = 'accepted';
        } elseif (isset($_POST['rejectBtn'])) {
            $status = 'rejected';
        }

        if (!empty($application_id)) {
            $updateStatusResult = updateApplicationStatus($pdo, $application_id, $status);

            if ($updateStatusResult) {
                $_SESSION['message'] = "Application status updated successfully.";
                $_SESSION['status'] = '200';
            } else {
                $_SESSION['message'] = "Failed to update application status.";
                $_SESSION['status'] = '500';
            }
        } else {
            $_SESSION['message'] = "Application ID is missing.";
            $_SESSION['status'] = '400';
        }

        header("Location: reviewapplications.php?post_id=" . $_GET['post_id']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Application</title>
    <link rel="stylesheet" href="styles/a-styles.css">
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

    <div class="application-review">
        <h1>Review Applications for <?php echo htmlspecialchars($job['job_title']); ?></h1>

        <!-- Table for displaying applicants -->
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Reason for Application</th>
                    <th>Status</th>
                    <th>Resume</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($application = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($application['name']); ?></td>
                        <td><?php echo htmlspecialchars($application['reason']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($application['status'])); ?></td>
                            <td>
                                <?php 
                                    if (!empty($application['pdf_path'])):
                                        $pdfPath = $application['pdf_path'];

                                        if (strpos($pdfPath, '/hr') === 0) {
                                            $pdfPath = str_replace('/hr', '', $pdfPath);
                                        }

                                        $baseUrl = 'http://localhost/findhire/';

                                        if (strpos($pdfPath, 'uploads/') !== 0) {
                                            $pdfPath = 'uploads/' . $pdfPath;
                                        }

                                        $fullPdfPath = $baseUrl . $pdfPath;
                                        echo '<a href="' . htmlspecialchars($fullPdfPath) . '" target="_blank">View Resume</a>';
                                    else:
                                        echo 'No resume uploaded.';
                                    endif;
                                ?>
                            </td>
                        <td>
                            <form action="reviewapplications.php?post_id=<?php echo $post_id; ?>" method="POST" style="display:inline;">
                                <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                                <input type="submit" name="acceptBtn" class="btn btn-success" value="Accept"></input>
                                <input type="submit" name="rejectBtn" class="btn btn-danger" value="Reject"></input>
                            </form>
                            <a href="allmessages.php?post_id=<?php echo $post_id; ?>&application_id=<?php echo $application['application_id']; ?>" class="btn btn-info" style="margin-top: 10px;">View Messages</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
