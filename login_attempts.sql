-- Create login_attempts table for tracking failed login attempts
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    locked_until TIMESTAMP NULL DEFAULT NULL,
    INDEX (ip_address),
    INDEX (attempt_time),
    INDEX (locked_until)
);

-- Create a stored procedure to clean up old login attempts
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS cleanup_login_attempts()
BEGIN
    -- Delete attempts older than 1 day
    DELETE FROM login_attempts 
    WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 1 DAY)
    AND (locked_until IS NULL OR locked_until < NOW());
END //
DELIMITER ;

-- Create an event to run the cleanup procedure daily
CREATE EVENT IF NOT EXISTS cleanup_login_attempts_event
ON SCHEDULE EVERY 1 DAY
DO
    CALL cleanup_login_attempts();
