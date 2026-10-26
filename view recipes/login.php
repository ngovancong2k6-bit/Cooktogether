<?php

// Connect to the MySQL database
$db = mysqli_connect('localhost', 'root', '', 'recipe_sharing_platform',3306);

// Check if the username and password exist
$username = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Kiểm tra giới hạn độ dài mật khẩu (tối đa 10 ký tự)
if (strlen($password) > 10) {
    echo '<script>alert("Mật khẩu bị quá giới hạn ký tự (tối đa 10 ký tự). Vui lòng thử lại!"); window.location.href="index.php";</script>';
    exit();
}

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
  echo '<script>alert("Email hoặc mật khẩu không chính xác. Vui lòng thử lại!"); window.location.href="index.php";</script>';
  exit();
}

