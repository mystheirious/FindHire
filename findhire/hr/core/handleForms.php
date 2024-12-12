<?php  
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = trim($_POST['username']);
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);
	$is_hr = true;

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			if (validatePassword($password)) {
				$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, password_hash($password, PASSWORD_DEFAULT), $is_hr);
				
				if ($insertQuery['status'] == '200') {
					$_SESSION['message'] = $insertQuery['message'];
					$_SESSION['status'] = $insertQuery['status'];
					header("Location: ../login.php");
				}

				else {
					$_SESSION['message'] = $insertQuery['message'];
					$_SESSION['status'] = $insertQuery['status'];
					header("Location: ../register.php");
				}
			}

			else {
				$_SESSION['message'] = "Password should be more than 8 characters and should contain both uppercase, lowercase, and numbers.";
				$_SESSION['status'] = "400";
				header("Location: ../register.php");
			}

		}

		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = "400";
			header("Location: ../register.php");
		}

	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = "400";
		header("Location: ../register.php");
	}
}


if (isset($_POST['loginUserBtn'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (!empty($username) && !empty($password)) {

		$loginQuery = checkIfUserExists($pdo, $username);
		$userIDFromDB = $loginQuery['userInfoArray']['user_id'];
		$usernameFromDB = $loginQuery['userInfoArray']['username'];
		$passwordFromDB = $loginQuery['userInfoArray']['password'];

		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['user_id'] = $userIDFromDB;
			$_SESSION['username'] = $usernameFromDB;
			header("Location: ../index.php");
		}

		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}

}


if (isset($_POST['insertNewPostBtn'])) {
    $job_title = $_POST['job_title'];
    $company = $_POST['company'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $added_by = $_SESSION['username'];

    $response = insertAPost($pdo, $job_title, $company, $description, $added_by, $status);


    if ($response['status'] == '200') {
        $_SESSION['status'] =  $insertAPost['status']; 
        $_SESSION['message'] =  $insertAPost['message']; 
        header("Location: ../index.php");
    } else {
        $_SESSION['message'] = "Please make sure there are no empty input fields";
        $_SESSION['status'] = '400';
        header("Location: ../index.php"); 
	}
}


if (isset($_POST['updatePostBtn'])) {
    $job_title = $_POST['job_title'];
    $company = $_POST['company'];
    $description = $_POST['description'];
    $status = $_POST['status']; 
    $date = date('Y-m-d H:i:s');

    if (!empty($job_title) && !empty($company) && !empty($description)) {
        $updatePost = updatePost($pdo, $job_title, $company, $description, $date, $_SESSION['username'], $status, $_GET['post_id']);
        $_SESSION['message'] = $updatePost['message'];
        $_SESSION['status'] = $updatePost['status'];
        header("Location: ../index.php");
    } else {
        $_SESSION['message'] = "Please make sure there are no empty input fields";
        $_SESSION['status'] = '400';
        header("Location: ../register.php");
    }
}


if (isset($_POST['deletePostBtn'])) {
	$post_id = $_GET['post_id'];

	if (!empty($post_id)) {
		$deletePost = deleteAPost($pdo, $post_id);
		$_SESSION['message'] = $deletePost['message'];
		$_SESSION['status'] = $deletePost['status'];
		header("Location: ../index.php");
	}
}


if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['username']);
	header("Location: ../login.php");
}


if (isset($_POST['insertReplyBtn'])) {
    $reply_description = $_POST['reply_description'];
    $message_id = $_POST['message_id'];
    $insertQuery = insertReply($pdo, $reply_description, $message_id, $_SESSION['user_id']);
    
    if ($insertQuery) {
        $_SESSION['message'] = "Reply added successfully";
        $_SESSION['status'] = "200";
        header("Location: ../hr/reply-to-message.php?message_id=" . $message_id);
    } else {
        $_SESSION['message'] = "Error adding reply";
        $_SESSION['status'] = "400"; 
        header("Location: ../hr/reply-to-message.php?message_id=" . $message_id);
    }
}


if (isset($_POST['updateReplyBtn'])) {
    $reply_description = $_POST['reply_description'];
    $reply_id = $_POST['reply_id'];
    $message_id = $_POST['message_id'];
    $editQuery = editReply($pdo, $reply_description, $reply_id);
    
    if ($editQuery) {
        $_SESSION['message'] = "Reply updated successfully";
        $_SESSION['status'] = "200";
        header("Location: ../hr/reply-to-message.php?message_id=" . $message_id);
    } else {
        $_SESSION['message'] = "Error updating reply";
        $_SESSION['status'] = "400"; 
        header("Location: ../hr/reply-to-message.php?message_id=" . $message_id);
    }
}


if (isset($_POST['deleteReplyBtn'])) {
    $reply_id = $_POST['reply_id'];
    $message_id = $_POST['message_id'];
    $deleteQuery = deleteReply($pdo, $reply_id);

    if ($deleteQuery) {
        $_SESSION['message'] = "Reply deleted successfully";
        $_SESSION['status'] = "200";
        header("Location: ../hr/reply-to-message.php?message_id=" . $message_id);
    } else {
        $_SESSION['message'] = "Error: Unable to delete reply";
        $_SESSION['status'] = "400";
        header("Location: ../hr/reply-to-message.php?message_id=" . $message_id);
    }
}


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
?>