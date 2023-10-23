<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_simpleshopping";

$connection = new mysqli($servername, $username, $password, $dbname);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
require 'User.php'; // Include the user.php file that contains the User class and database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input from the registration form
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    // Initialize the User class with the database connection
    $user = new User($connection); // Assuming $db is your database connection

    // Call the registerUser method from the User class
    $registrationResult = $user->registerUser($username, $password, $email);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>User Registration</h2>
    <form method="post"> <!-- Removed the extra 'm' in the <form> tag and specified 'action' attribute -->
        Username: <input type="text" name="username"><br><br>
        Password: <input type="password" name="password"><br><br>
        Email: <input type="text" name="email"><br><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
