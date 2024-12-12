<?php 
require_once 'core/models.php'; 
require_once 'core/dbConfig.php';
 
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
	<title>Delete Post</title>
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

	<h1>Are you sure you want to delete this job post?</h1>
	<?php $getPostByID = getPostByID($pdo, $_GET['post_id']); ?>
		<form action="core/handleForms.php?post_id=<?php echo $_GET['post_id']; ?>" method="POST">
			<h2>Job Title: <?php echo $getPostByID['job_title']; ?></h2>
			<h2>Company: <?php echo $getPostByID['company']; ?></h2>
			<h2>Job Description: <?php echo $getPostByID['description']; ?></h2>
			<input type="submit" name="deletePostBtn" value="Delete">
		</form>			
	</div>
</body>
</html>