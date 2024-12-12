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
    <title>HR Dashboard</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Searching query -->
    <div class="searchForm">
        <form action="index.php" method="GET">
            <p>
                <input type="text" name="searchQuery" placeholder="Search here">
                <input type="submit" name="searchBtn" value="Search">
            </p>
        </form>
            <p><a href="index.php">Search Again</a></p>  
    </div>

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

    <!-- Job posts details -->
    <div class="tableClass">
        <table style="width: 100%;" cellpadding="20"> 
            <tr>
                <th>Job Title</th>
                <th>Company</th>
                <th>Job Description</th>
                <th>Status</th>
                <th>Date Added</th>
                <th>Added By</th>
                <th>Last Updated</th>
                <th>Last Updated By</th>
                <th>Action</th>
            </tr>

            <?php if (!isset($_GET['searchBtn'])) { ?>
                <?php $getAllPosts = getAllPosts($pdo); ?>
                <?php foreach ($getAllPosts as $row) { ?>
                <tr>
                    <td><?php echo $row['job_title']; ?></td>
                    <td><?php echo $row['company']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['date_added']; ?></td>
                    <td><?php echo $row['added_by']; ?></td>
                    <td><?php echo $row['last_updated']; ?></td>
                    <td><?php echo $row['last_updated_by']; ?></td>
                    <td>
                        <a href="updatepost.php?post_id=<?php echo $row['post_id']; ?>">Update</a>
                        <a href="deletepost.php?post_id=<?php echo $row['post_id']; ?>">Delete</a>
                        <a href="reviewapplications.php?post_id=<?php echo $row['post_id']; ?>">View Applicants</a>
                    </td>
                </tr>
                <?php } ?>
            <?php } else { ?>
                <?php $getAllPostsBySearch = getAllPostsBySearch($pdo, $_GET['searchQuery']); ?>
                <?php foreach ($getAllPostsBySearch as $row) { ?>
                <tr>
                    <td><?php echo $row['job_title']; ?></td>
                    <td><?php echo $row['company']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['date_added']; ?></td>
                    <td><?php echo $row['added_by']; ?></td>
                    <td><?php echo $row['last_updated']; ?></td>
                    <td><?php echo $row['last_updated_by']; ?></td>
                    <td>
                        <a href="updatepost.php?post_id=<?php echo $row['post_id']; ?>">Update</a>
                        <a href="deletepost.php?post_id=<?php echo $row['post_id']; ?>">Delete</a>
                        <a href="reviewapplications.php?post_id=<?php echo $row['post_id']; ?>">View Applicants</a>
                    </td>
                </tr>
                <?php } ?>
            <?php } ?>
        </table>
    </div>

</body>
</html>