<?php
// Database connection parameters
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'kissan_agro_foods';

// Connect to database
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Admin user details
$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$email = 'admin@kissanagrofoods.com';
$full_name = 'Administrator';
$role = 'admin';

// Check if user already exists
$check_query = "SELECT id FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($result) > 0) {
    // Update existing user
    $query = "UPDATE users SET 
              password = '$password',
              email = '$email',
              full_name = '$full_name',
              role = '$role'
              WHERE username = '$username'";
    
    if (mysqli_query($conn, $query)) {
        echo "Admin user updated successfully!";
    } else {
        echo "Error updating admin user: " . mysqli_error($conn);
    }
} else {
    // Create new user
    $query = "INSERT INTO users (username, password, email, full_name, role) 
              VALUES ('$username', '$password', '$email', '$full_name', '$role')";
    
    if (mysqli_query($conn, $query)) {
        echo "Admin user created successfully!";
    } else {
        echo "Error creating admin user: " . mysqli_error($conn);
    }
}

// Close connection
mysqli_close($conn);
?>
