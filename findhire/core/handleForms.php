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


if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['user_id']);
	unset($_SESSION['username']);
	header("Location: ../login.php");
}


if (isset($_POST['insertMessageBtn'])) {
    $post_id = $_POST['post_id'];  
    $application_id = $_POST['application_id'];  
    $message_description = $_POST['message_description'];
    $insertQuery = insertMessage($pdo, $_SESSION['user_id'], $post_id, $application_id, $message_description);
    
    if ($insertQuery) {
        $_SESSION['message'] = "Message inserted successfully";
        $_SESSION['status'] = "200";
        header("Location: ../messages.php?post_id=" . $post_id . "&application_id=" . $application_id);
        exit();
    } else {
        $_SESSION['message'] = "Error inserting message";
        $_SESSION['status'] = "400";
        header("Location: ../messages.php?post_id=" . $post_id . "&application_id=" . $application_id);
        exit();
    }
}


if (isset($_POST['editMessageBtn'])) {
    $message_id = $_POST['message_id'];
    $message_description = $_POST['message_description'];
    $post_id = $_POST['post_id'];
    $application_id = $_POST['application_id'];

    if (empty($message_description)) {
        echo "Description cannot be empty.";
        exit();
    }

    $updateQuery = editMessage($pdo, $message_description, $message_id);

    if ($updateQuery) {
        $_SESSION['message'] = "Message updated successfully";
        $_SESSION['status'] = "200";
        header("Location: ../messages.php?post_id=" . $post_id . "&application_id=" . $application_id);
        exit();
    } else {
        $_SESSION['message'] = "Error updating the message";
        $_SESSION['status'] = "400"; //
        echo "Error updating the message.";
    }
}


if (isset($_POST['deleteMessageBtn'])) {
    $message_id = $_POST['message_id'];
    $post_id = $_POST['post_id'] ?? null; 
    $application_id = $_POST['application_id'] ?? null;  

    if (empty($message_id)) {
        echo "Invalid message ID.";
        exit();
    }

    $deleteQuery = deleteMessage($pdo, $message_id);

    if ($deleteQuery) {
        $_SESSION['message'] = "Message deleted successfully";
        $_SESSION['status'] = "200";
        header("Location: ../messages.php?post_id=" . $post_id . "&application_id=" . $application_id);
        exit();
    } else {
        $_SESSION['message'] = "Error: Unable to delete the message";
        $_SESSION['status'] = "400";
        echo "Error: Unable to delete the message.";
    }
}


if (isset($_POST['submitApplicationBtn'])) {
    $post_id = $_POST['post_id'];
    $reason = $_POST['reason'];
    $status = $_POST['status'];
    $post_status = $_POST['post_status'];


    if (!empty($_FILES['pdf']['name'])) {
        $fileName = $_FILES['pdf']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $uniqueID = sha1(md5(rand(1, 9999999)));
        $pdfName = $uniqueID . "." . $fileExtension;
        $pdfPath = "../uploads/" . $pdfName;

        if ($fileExtension === 'pdf') {
            if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath)) {
                $applicationInserted = submitApplication($pdo, $_SESSION['user_id'], $post_id, $reason, $pdfPath, $status);

                if ($applicationInserted) {
                    $_SESSION['message'] = "Application submitted successfully.";
                    $_SESSION['status'] = "200";
                } else {
                    $_SESSION['message'] = "Failed to save application. Please try again.";
                    $_SESSION['status'] = "400";
                }
            } else {
                $_SESSION['message'] = "Failed to upload the PDF file. Please try again.";
                $_SESSION['status'] = "400";
            }
        } else {
            $_SESSION['message'] = "Invalid file type. Only PDF files are allowed.";
            $_SESSION['status'] = "400";
        }
    } else {
        $_SESSION['message'] = "Please upload a PDF file.";
        $_SESSION['status'] = "400";
    }

	header("Location: ../index.php");
    exit();
}


if (isset($_POST['updateApplicationBtn'])) {
    $post_id = $_GET['post_id'] ?? null;
    $application_id = $_GET['application_id'] ?? null;

    if ($post_id && $application_id) {
        $new_reason = $_POST['reason'] ?? '';
        $new_pdf_path = null;

        if (!empty($_FILES['pdf']['name'])) {
            $fileName = $_FILES['pdf']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $uniqueID = sha1(md5(rand(1, 9999999)));
            $pdfName = $uniqueID . "." . $fileExtension;
            $pdfPath = "uploads/" . $pdfName;

            if ($fileExtension === 'pdf') {
                if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath)) {
                    $new_pdf_path = $pdfPath; // Set the new PDF path for the database update
                } else {
                    $_SESSION['message'] = "Failed to upload the PDF file. Please try again.";
                    $_SESSION['status'] = "400";
                    header("Location: viewApplication.php?post_id=$post_id&application_id=$application_id");
                    exit();
                }
            } else {
                $_SESSION['message'] = "Invalid file type. Only PDF files are allowed.";
                $_SESSION['status'] = "400";
                header("Location: viewApplication.php?post_id=$post_id&application_id=$application_id");
                exit();
            }
        }

        $updateStatus = updateApplication($pdo, $application_id, $new_reason, $new_pdf_path);
        if ($updateStatus) {
            $_SESSION['message'] = "Application updated successfully!";
            $_SESSION['status'] = '200'; // Success status code
            header("Location: viewApplication.php?post_id=$post_id&application_id=$application_id");
        } else {
            $_SESSION['message'] = "Failed to update the application.";
            $_SESSION['status'] = '500'; // Error status code
            header("Location: viewApplication.php?post_id=$post_id&application_id=$application_id");
        }
    } else {
        $_SESSION['message'] = "Post ID or Application ID is missing!";
        $_SESSION['status'] = '400';
        header("Location: viewApplication.php?post_id=$post_id&application_id=$application_id");
    }
}


if (isset($_POST['withdrawApplicationBtn'])) {
    $post_id = $_GET['post_id'] ?? null;
    $application_id = $_GET['application_id'] ?? null;

    if ($post_id && $application_id) {
        $withdrawStatus = withdrawApplication($pdo, $application_id);
        
        if ($withdrawStatus) {
            $_SESSION['message'] = "Application withdrawn successfully!";
            $_SESSION['status'] = '200';
            header("Location: index.php"); 
            exit();
        } else {
            $_SESSION['message'] = "Failed to withdraw the application. Please try again.";
            $_SESSION['status'] = '500';
            header("Location: viewApplication.php?post_id=$post_id&application_id=$application_id"); 
            exit();
        }
    } else {
        $_SESSION['message'] = "Post ID or Application ID is missing!";
        $_SESSION['status'] = '400';
        header("Location: viewApplication.php?post_id=$post_id&application_id=$application_id");
        exit();
    }
}
?>