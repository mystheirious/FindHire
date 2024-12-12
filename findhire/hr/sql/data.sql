CREATE TABLE posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    job_title VARCHAR(255),
    company VARCHAR(255),
    description TEXT,
    status ENUM('active', 'closed') DEFAULT 'active',
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by VARCHAR(255),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_updated_by VARCHAR(255),
    accepted_applicant_id INT DEFAULT NULL
);


CREATE TABLE activity_logs (
	activity_log_id INT AUTO_INCREMENT PRIMARY KEY,
	operation VARCHAR(255),
	post_id INT,
	job_title VARCHAR(255),
	company VARCHAR(255),
	description TEXT,
	status ENUM('active', 'closed') DEFAULT 'active',
	username VARCHAR(255),
	date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);


CREATE TABLE user_accounts (
	user_id INT AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(255),
	first_name VARCHAR(255),
	last_name VARCHAR(255),
	password TEXT,
	is_hr TINYINT(1) NOT NULL DEFAULT 0,
	date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    post_id INT,
    reason TEXT,
    pdf_path VARCHAR(255),
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    date_applied TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
