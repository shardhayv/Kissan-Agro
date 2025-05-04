-- Create visitor_logs table for tracking site visitors
CREATE TABLE IF NOT EXISTS visitor_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255),
    page_url VARCHAR(255) NOT NULL,
    referrer_url VARCHAR(255),
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    session_id VARCHAR(255),
    country VARCHAR(100),
    city VARCHAR(100),
    browser VARCHAR(100),
    os VARCHAR(100),
    device_type VARCHAR(50),
    INDEX (ip_address),
    INDEX (visit_time),
    INDEX (page_url(191)),
    INDEX (session_id(191))
);

-- Create a stored procedure to clean up old visitor logs
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS cleanup_visitor_logs()
BEGIN
    -- Delete logs older than 30 days
    DELETE FROM visitor_logs 
    WHERE visit_time < DATE_SUB(NOW(), INTERVAL 30 DAY);
END //
DELIMITER ;

-- Create an event to run the cleanup procedure daily
CREATE EVENT IF NOT EXISTS cleanup_visitor_logs_event
ON SCHEDULE EVERY 1 DAY
DO
    CALL cleanup_visitor_logs();
