<?php
// Đường dẫn file users (CSDL giả lập)
$usersFile = 'users.json';

// Nhận username từ form
$username = $_POST['username'] ?? '';

if ($username === '') {
    echo "Vui lòng nhập tên đăng nhập.";
    exit;
}

// Đọc dữ liệu người dùng
if (!file_exists($usersFile)) {
    echo "Không tìm thấy dữ liệu người dùng.";
    exit;
}

$json = file_get_contents($usersFile);
$users = json_decode($json, true);

// Tìm người dùng
$found = false;
foreach ($users as $user) {
    if ($user['username'] === $username) {
        $found = true;
        break;
    }
}

if (!$found) {
    echo "Tên đăng nhập không tồn tại.";
    exit;
}

// Giả lập gửi thông báo khôi phục mật khẩu
echo "Yêu cầu khôi phục mật khẩu đã được gửi đến email (giả lập).";
// Hoặc chuyển hướng về trang login sau vài giây
// header("refresh:3;url=/index.php");
?>
