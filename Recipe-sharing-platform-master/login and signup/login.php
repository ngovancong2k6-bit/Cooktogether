<?php

// Connect to the MySQL database
$db = mysqli_connect('localhost', 'root', '', 'recipe_sharing_platform',3306);

// Check if the username and password exist
$username = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT id FROM users WHERE email = ? AND password = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('ss', $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  // The user exists, so log them in
  // echo 'valid username or password.';
  session_start();
    $_SESSION['user_id'] = $result->fetch_row()[0];
    
    header("Location: index3.php");
    exit();
  // Redirect the user to the home page
  // header('Location: index.php');
} else {
  // The user does not exist, so display an error message
  echo 'Invalid username or password.';
}

