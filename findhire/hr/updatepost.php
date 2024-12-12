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
    <title>Update Job Post</title>
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

    <?php 
    $getPostByID = getPostByID($pdo, $_GET['post_id']); 
    ?>
    
    <form action="core/handleForms.php?post_id=<?php echo $_GET['post_id']; ?>" method="POST">
        <p>
            <label for="job_title">Job Title</label>
            <input type="text" name="job_title" value="<?php echo $getPostByID['job_title']; ?>" required>
        </p>
        <p>
            <label for="company">Company</label>
            <input type="text" name="company" value="<?php echo $getPostByID['company']; ?>" required>
        </p>
        <p>
            <label for="description">Job Description</label>
            <input type="text" name="description" value="<?php echo $getPostByID['description']; ?>" required>
        </p>
        <p>
            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="pending" <?php echo ($getPostByID['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="active" <?php echo ($getPostByID['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="closed" <?php echo ($getPostByID['status'] == 'closed') ? 'selected' : ''; ?>>Closed</option>
            </select>
        </p>
        <p>
            <input type="submit" name="updatePostBtn" value="Update">
        </p>
    </form>
</body>
</html>
