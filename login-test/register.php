<?php
// Đường dẫn file users (CSDL giả lập)
$usersFile = 'users.json';

// Nhận dữ liệu từ form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm'] ?? '';

// Kiểm tra dữ liệu
if ($username === '' || $password === '' || $confirm === '') {
    echo "Vui lòng điền đầy đủ thông tin nha.";
    exit;
}

if ($password !== $confirm) {
    echo "Mật khẩu và xác nhận không khớp.";
    exit;
}

// Kiểm tra ký tự unicode (không cho phép)
if (preg_match('/[^\x00-\x7F]/', $username) || preg_match('/[^\x00-\x7F]/', $password)) {
    echo "Không được dùng kí tự unicode.";
    exit;
}

// Đọc dữ liệu người dùng hiện có
$users = [];
if (file_exists($usersFile)) {
    $json = file_get_contents($usersFile);
    $users = json_decode($json, true);
}

// Kiểm tra username đã tồn tại
foreach ($users as $user) {
    if ($user['username'] === $username) {
        echo "Tên đăng nhập đã tồn tại.";
        exit;
    }
}

// Thêm người dùng mới
$users[] = [
    'username' => $username,
    'password' => password_hash($password, PASSWORD_DEFAULT)
];

// Ghi lại file
file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

// Chuyển hướng về login
header("Location: /index.php");
exit;
?>
    