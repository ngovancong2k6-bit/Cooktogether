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
if (strlen($password) < 4 || strlen($password) > 20) {
    echo '<script>alert("Mật khẩu phải có độ dài từ 4 đến 20 ký tự."); window.history.back();</script>';
}    else   
    {
    // Continue with the registration process
    $checkUserQuery = "SELECT * FROM users WHERE email='$email'";
    $checkUserResult = $db->query($checkUserQuery);

    if ($checkUserResult->num_rows > 0) {
        echo '<script>alert("Email này đã được sử dụng. Vui lòng chọn email khác!"); window.history.back();</script>';
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('sss', $name, $email, $password);
        
        if ($stmt->execute()) {
            echo '<script type="text/javascript">';
            echo 'alert("Đăng ký tài khoản thành công! Vui lòng đăng nhập.");';
            echo 'window.location.href = "index.php";';
            echo '</script>';
            exit();
        } else {
            echo '<script>alert("Đăng ký thất bại. Vui lòng thử lại sau!"); window.history.back();</script>';
        }
    }
}
