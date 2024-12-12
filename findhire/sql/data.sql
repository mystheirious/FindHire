CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT,
    user_id INT,
    post_id INT,
    application_id INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE replies (
    reply_id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT,
    message_id INT,
    user_id INT,
    post_id INT,
    application_id INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
