<?php  
require_once 'dbConfig.php';

function checkIfUserExists($pdo, $username) {
	$response = array();
	$sql = "SELECT * FROM user_accounts WHERE username = ?";
	$stmt = $pdo->prepare($sql);

	if ($stmt->execute([$username])) {

		$userInfoArray = $stmt->fetch();

		if ($stmt->rowCount() > 0) {
			$response = array(
				"result"=> true,
				"status" => "200",
				"userInfoArray" => $userInfoArray
			);
		}

		else {
			$response = array(
				"result"=> false,
				"status" => "400",
				"message"=> "User doesn't exist from the database"
			);
		}
	}

	return $response;
}


function insertNewUser($pdo, $username, $first_name, $last_name, $password) {
	$response = array();
	$checkIfUserExists = checkIfUserExists($pdo, $username); 

	if (!$checkIfUserExists['result']) {

		$sql = "INSERT INTO user_accounts (username, first_name, last_name, password) 
		VALUES (?,?,?,?)";

		$stmt = $pdo->prepare($sql);

		if ($stmt->execute([$username, $first_name, $last_name, $password])) {
			$response = array(
				"status" => "200",
				"message" => "User successfully inserted!"
			);
		}

		else {
			$response = array(
				"status" => "400",
				"message" => "An error occured with the query!"
			);
		}
	}

	else {
		$response = array(
			"status" => "400",
			"message" => "User already exists!"
		);
	}

	return $response;
}


function validatePassword($password) {
    if (strlen($password) >= 8) {
        $hasLower = false;
        $hasUpper = false;
        $hasNumber = false;

        for ($i = 0; $i < strlen($password); $i++) {
            if (ctype_lower($password[$i])) {
                $hasLower = true;
            } elseif (ctype_upper($password[$i])) {
                $hasUpper = true;
            } elseif (ctype_digit($password[$i])) {
                $hasNumber = true;
            }

            if ($hasLower && $hasUpper && $hasNumber) {
                return true;
            }
        }
    }

    return false;
}


function getAllPosts($pdo) {
	$sql = "SELECT * FROM posts ORDER BY date_added DESC";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}


function getAllUsers($pdo) {
	$sql = "SELECT * FROM user_accounts";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}


function getUserByID($pdo, $user_id) {
	$sql = "SELECT * FROM user_accounts WHERE user_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$user_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}


function insertMessage($pdo, $user_id, $post_id, $application_id, $description) {
    $sql = "INSERT INTO messages (user_id, post_id, application_id, description, date_added) 
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$user_id, $post_id, $application_id, $description]);
}


function getMessageByID($pdo, $message_id) {
	$sql = "SELECT * FROM messages WHERE message_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$message_id]);
	if ($executeQuery) {
		return $stmt->fetch();
	}
}


function editMessage($pdo, $description, $message_id) {
    $sql = "UPDATE messages SET description = ? WHERE message_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$description, $message_id]);
}


function deleteMessage($pdo, $message_id) {
	$sql = "DELETE FROM messages WHERE message_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$message_id]);
	if ($executeQuery) {
		return true;
	}
}


function getAllMessages($pdo, $message_id=NULL) {

	if (!empty($message_id)) {
		$sql = "SELECT 
					user_accounts.username AS username,
					messages.message_id AS message_id,
					messages.description AS description,
					messages.date_added AS date_added
				FROM messages
				JOIN user_accounts 
				ON messages.user_id = user_accounts.user_id
				WHERE messages.message_id = ?
				";
		$stmt = $pdo->prepare($sql);
		$executeQuery = $stmt->execute([$message_id]);

		if ($executeQuery) {
			return $stmt->fetch();
		}

	}
	else {
		$sql = "SELECT 
					user_accounts.username AS username,
					messages.message_id AS message_id,
					messages.description AS description,
					messages.date_added AS date_added
				FROM messages
				JOIN user_accounts 
				ON messages.user_id = user_accounts.user_id
				";
		$stmt = $pdo->prepare($sql);
		$executeQuery = $stmt->execute();

		if ($executeQuery) {
			return $stmt->fetchAll();
		}

	}
}


function getMessagesForPost($pdo, $user_id, $post_id) {
    if (!empty($post_id)) {
        $sql = "SELECT 
                    user_accounts.username AS username,
                    messages.message_id AS message_id,
                    messages.description AS description,
                    messages.date_added AS date_added
                FROM messages
                JOIN user_accounts 
                ON messages.user_id = user_accounts.user_id
                WHERE messages.post_id = ? AND messages.user_id = ?";
        $stmt = $pdo->prepare($sql);
        $executeQuery = $stmt->execute([$post_id, $user_id]);

        if ($executeQuery) {
            return $stmt->fetchAll(); 
        }
    }
    return [];  
}


function getAllRepliesByMessage($pdo, $message_id) {
    $sql = "SELECT 
                user_accounts.username AS username,
                replies.reply_id AS reply_id,
                replies.description AS description,
                replies.date_added AS date_added
            FROM replies
            JOIN user_accounts 
            ON replies.user_id = user_accounts.user_id
            JOIN messages 
            ON replies.message_id = messages.message_id
            WHERE messages.message_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$message_id]);

    if ($executeQuery) {
        return $stmt->fetchAll();
    } else {
        return []; 
    }
}


function getPostByID($pdo, $post_id) {
	$sql = "SELECT * FROM posts WHERE post_id = ?";
	$stmt = $pdo->prepare($sql);
	if ($stmt->execute([$post_id])) {
		return $stmt->fetch();
	}
}


function submitApplication($pdo, $user_id, $post_id, $reason, $pdfPath, $status = 'pending') {
    $sql = "INSERT INTO applications (user_id, post_id, reason, pdf_path, status) VALUES (?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);

    return $stmt->execute([$user_id, $post_id, $reason, $pdfPath, $status]);
}


function checkIfApplied($pdo, $user_id, $post_id) {
    $sql = "SELECT application_id FROM applications WHERE user_id = ? AND post_id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$user_id, $post_id])) {
        $application_id = $stmt->fetchColumn();
        
        return $application_id ? $application_id : false;
    }

    return false;
}


function getApplicationById($pdo, $post_id, $user_id) {
    $stmt = $pdo->prepare("
        SELECT applications.*, user_accounts.username 
        FROM applications 
        JOIN user_accounts ON applications.user_id = user_accounts.user_id 
        WHERE applications.post_id = :post_id AND applications.user_id = :user_id
    ");
    
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        return $result;
    }
    
    return false;
}


function updateApplication($pdo, $application_id, $reason, $pdfPath) {
    $sql = "UPDATE applications SET reason = ?, pdf_path = ? WHERE application_id = ?";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$reason, $pdfPath, $application_id]);
    
    return $executeQuery;
}


function withdrawApplication($pdo, $application_id) {
    $sql = "DELETE FROM applications WHERE application_id = ?";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$application_id]);
    
    return $executeQuery;
}