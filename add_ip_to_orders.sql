-- Add IP address column to orders table
ALTER TABLE orders ADD COLUMN customer_ip VARCHAR(45) DEFAULT NULL AFTER customer_address;

-- Add index for IP address
ALTER TABLE orders ADD INDEX (customer_ip);
