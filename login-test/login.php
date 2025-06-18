<?php
session_start();

$usersFile = 'users.json';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Kiểm tra dữ liệu rỗng
if ($username === '' || $password === '') {
    echo "Điền đầy đủ username và password";
    exit;
}

// Kiểm tra ký tự unicode (không hợp lệ)
if (preg_match('/[^\x00-\x7F]/', $username)) {
    echo "username không được dùng kí tự unicode";
    exit;
}
if (preg_match('/[^\x00-\x7F]/', $password)) {
    echo "password không được dùng kí tự unicode";
    exit;
}

// Đọc dữ liệu người dùng từ file JSON
if (!file_exists($usersFile)) {
    echo "Không tìm thấy dữ liệu người dùng";
    exit;
}

$json = file_get_contents($usersFile);

$users = json_decode($json, true);

if ($users === null) {
    echo "Lỗi đọc dữ liệu người dùng (JSON không hợp lệ)";
    exit;
}


// Tìm user
$found = false;
foreach ($users as $user) {
    if ($user['username'] === $username) {
        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            $found = true;
            $_SESSION['username'] = $username;

            // Nếu nhớ đăng nhập
            if ($remember) {
                setcookie('remember', $username, time() + (86400 * 30), "/"); // 30 ngày
            }

            // Chuyển về trang chính
            header("Location: /home.html");
            exit;
        } else {
            echo "sai thông tin đăng nhập"; // Mật khẩu sai
            exit;
        }
    }
}

if (!$found) {
    echo "sai thông tin đăng nhập"; // Username không tồn tại
    exit;
}
?>
