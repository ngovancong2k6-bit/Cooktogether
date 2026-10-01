<?php

// Connect to the MySQL database
$db = mysqli_connect('localhost', 'root', '', 'recipe_sharing_platform', 3306);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get the user's information from the POST request
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Check password length client-side 
if (strlen($password) < 4 || strlen($password) > 10) {
    echo "Password length must be between 4 and 10 characters.";
}    else   
    {
    // Continue with the registration process
    $checkUserQuery = "SELECT * FROM users WHERE email='$email'";
    $checkUserResult = $db->query($checkUserQuery);

    if ($checkUserResult->num_rows > 0) {
        echo "Username already exists. Please choose a different one.";
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('sss', $name, $email, $password);
        
        if ($stmt->execute()) {
            echo '<script type="text/javascript">';
            echo 'alert("Registration is Successful!!!")';
            echo '</script>';
            header("Location: index1.php");
        } else {
            echo "Registration failed.";
        }
    }
}
