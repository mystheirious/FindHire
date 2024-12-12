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


function getUserByID($pdo, $user_id) {
	$sql = "SELECT * FROM user_accounts WHERE user_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$user_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}


function insertNewUser($pdo, $username, $first_name, $last_name, $password, $is_hr) {
	$response = array();
	$checkIfUserExists = checkIfUserExists($pdo, $username); 

	if (!$checkIfUserExists['result']) {

		$sql = "INSERT INTO user_accounts (username, first_name, last_name, password, is_hr) 
		VALUES (?,?,?,?,?)";

		$stmt = $pdo->prepare($sql);

		if ($stmt->execute([$username, $first_name, $last_name, $password, $is_hr])) {
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


function getAllHRs($pdo) {
	$sql = "SELECT * FROM user_accounts 
			WHERE is_hr = 1";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}


function getAllUsers($pdo) {
	$sql = "SELECT * FROM user_accounts 
			WHERE is_hr = 0";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}


function getAllPosts($pdo) {
	$sql = "SELECT * FROM posts";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}


function getAllPostsBySearch($pdo, $search_query) {
	$sql = "SELECT * FROM posts WHERE 
			CONCAT(job_title,company,
				description,
				date_added,added_by,
				last_updated,
				last_updated_by) 
			LIKE ?";

	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute(["%".$search_query."%"]);
	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}


function getPostByID($pdo, $post_id) {
	$sql = "SELECT * FROM posts WHERE post_id = ?";
	$stmt = $pdo->prepare($sql);
	if ($stmt->execute([$post_id])) {
		return $stmt->fetch();
	}
}


function insertAnActivityLog($pdo, $operation, $post_id, $job_title, 
        $company, $description, $status, $username) {
    
    if (empty($status)) {
        $status = 'active';
    }

    $sql = "INSERT INTO activity_logs (operation, post_id, job_title, 
            company, description, status, username) VALUES(?,?,?,?,?,?,?)";

    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$operation, $post_id, $job_title, 
            $company, $description, $status, $username]);

    if ($executeQuery) {
        return true;
    }

    return false;
}


function getAllActivityLogs($pdo) {
	$sql = "SELECT * FROM activity_logs 
			ORDER BY date_added DESC";
	$stmt = $pdo->prepare($sql);
	if ($stmt->execute()) {
		return $stmt->fetchAll();
	}
}


function insertAPost($pdo, $job_title, $company, $description, $added_by, $status) {
    $response = array();
    $sql = "INSERT INTO posts (job_title, company, description, added_by, status) VALUES(?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $insertpost = $stmt->execute([$job_title, $company, $description, $added_by, $status]);

    if ($insertpost) {
        $findInsertedItemSQL = "SELECT * FROM posts ORDER BY date_added DESC LIMIT 1";
        $stmtfindInsertedItemSQL = $pdo->prepare($findInsertedItemSQL);
        $stmtfindInsertedItemSQL->execute();
        $getpostID = $stmtfindInsertedItemSQL->fetch();

        $insertAnActivityLog = insertAnActivityLog($pdo, "INSERT", $getpostID['post_id'], 
            $getpostID['job_title'], $getpostID['company'], 
            $getpostID['description'], $getpostID['status'], $_SESSION['username']);

        if ($insertAnActivityLog) {
            $response = array(
                "status" => "200",
                "message" => "Post added successfully!"
            );
        } else {
            $response = array(
                "status" => "400",
                "message" => "Insertion of activity log failed!"
            );
        }
    } else {
        $response = array(
            "status" => "400",
            "message" => "Insertion of data failed!"
        );
    }

    return $response;
}


function updatePost($pdo, $job_title, $company, $description, $last_updated, $last_updated_by, $status, $post_id) {
    $response = array();
    $sql = "UPDATE posts
            SET job_title = ?,
                company = ?,
                description = ?, 
                last_updated = ?, 
                last_updated_by = ?, 
                status = ?
            WHERE post_id = ?";
    $stmt = $pdo->prepare($sql);
    $updatepost = $stmt->execute([$job_title, $company, $description, $last_updated, $last_updated_by, $status, $post_id]);

    if ($updatepost) {
        $findInsertedItemSQL = "SELECT * FROM posts WHERE post_id = ?";
        $stmtfindInsertedItemSQL = $pdo->prepare($findInsertedItemSQL);
        $stmtfindInsertedItemSQL->execute([$post_id]);
        $getpostID = $stmtfindInsertedItemSQL->fetch();

        $insertAnActivityLog = insertAnActivityLog($pdo, "UPDATE", $getpostID['post_id'], 
            $getpostID['job_title'], $getpostID['company'], 
            $getpostID['description'], $getpostID['status'], $_SESSION['username']);

        if ($insertAnActivityLog) {
            $response = array(
                "status" => "200",
                "message" => "Updated the post successfully!"
            );
        } else {
            $response = array(
                "status" => "400",
                "message" => "Insertion of activity log failed!"
            );
        }
    } else {
        $response = array(
            "status" => "400",
            "message" => "An error has occurred with the query!"
        );
    }

    return $response;
}


function deleteAPost($pdo, $post_id) {
	$response = array();
	$sql = "SELECT * FROM posts WHERE post_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$post_id]);
	$getPostByID = $stmt->fetch();

	$insertAnActivityLog = insertAnActivityLog($pdo, "DELETE", $getPostByID['post_id'], 
		$getPostByID['job_title'], $getPostByID['company'], 
		$getPostByID['description'], $getPostByID['status'], $_SESSION['username']);

	if ($insertAnActivityLog) {
		$deleteSql = "DELETE FROM posts WHERE post_id = ?";
		$deleteStmt = $pdo->prepare($deleteSql);
		$deleteQuery = $deleteStmt->execute([$post_id]);

		if ($deleteQuery) {
			$response = array(
				"status" =>"200",
				"message"=>"Deleted the post successfully!"
			);
		}
		else {
			$response = array(
				"status" =>"400",
				"message"=>"Insertion of activity log failed!"
			);
		}
	}
	else {
		$response = array(
			"status" =>"400",
			"message"=>"An error has occured with the query!"
		);
	}

	return $response;
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
				ORDER BY messages.date_added DESC
				";
		$stmt = $pdo->prepare($sql);
		$executeQuery = $stmt->execute();

		if ($executeQuery) {
			return $stmt->fetchAll();
		}

	}
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
			WHERE messages.message_id = ?
			";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$message_id]);

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}


function insertReply($pdo, $description, $message_id, $user_id) {
	$sql = "INSERT INTO replies (description, message_id, user_id) VALUES(?,?,?)";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$description, $message_id, $user_id]);
	if ($executeQuery) {
		return true;
	}
}


function getReplyByID($pdo, $reply_id) {
	$sql = "SELECT * FROM replies WHERE reply_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$reply_id]);
	if ($executeQuery) {
		return $stmt->fetch();
	}
}


function editReply($pdo, $description, $reply_id) {
	$sql = "UPDATE replies SET description = ? WHERE reply_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$description, $reply_id]);
	if ($executeQuery) {
		return true;
	}
}


function deleteReply($pdo, $reply_id) {
	$sql = "DELETE FROM replies WHERE reply_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$reply_id]);
	if ($executeQuery) {
		return true;
	}
}


function updateApplicationStatus($pdo, $application_id, $status) {
    $sql = "UPDATE applications SET status = ? WHERE application_id = ?";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$status, $application_id]);
    if ($executeQuery) {
        return true;
    }
}


function getMessagesForApplication($pdo, $application_id) {
    $query = "
        SELECT 
            user_accounts.username AS username, 
            messages.message_id, 
            messages.date_added, 
            messages.description
        FROM 
            messages
        JOIN 
            applications
        ON 
            messages.application_id = applications.application_id
        JOIN 
            user_accounts
        ON 
            applications.user_id = user_accounts.user_id
        WHERE 
            applications.application_id = :application_id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['application_id' => $application_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>