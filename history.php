<?php
// Kết nối database
$conn = new mysqli("localhost", "root", "", "library");
if ($conn->connect_error) {
    die("Lỗi kết nối DB: " . $conn->connect_error);
}

// Fake session login
session_start();
$_SESSION['user_id'] = 1; // admin
$_SESSION['role'] = 'admin';

// Hàm ghi log hoạt động
function log_activity($conn, $user_id, $action) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $action, $ip, $agent);
    $stmt->execute();
}

// Ghi log mỗi lần truy cập index
log_activity($conn, $_SESSION['user_id'], "Truy cập trang chính");

// Giao diện
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lịch sử truy cập - Quản lý Thư viện</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
        th { background: #f2f2f2; }
        h2 { color: #333; }
    </style>
</head>
<body>

<h2>👨‍💼 Xin chào Admin! Đây là lịch sử truy cập hệ thống:</h2>

<?php
// Chỉ admin mới được xem
if ($_SESSION['role'] !== 'admin') {
    echo "<p>Bạn không có quyền truy cập.</p>";
    exit;
}

$query = "SELECT logs.*, users.username 
          FROM activity_logs logs 
          JOIN users ON logs.user_id = users.id 
          ORDER BY logs.created_at DESC";

$result = $conn->query($query);

if ($result->num_rows === 0) {
    echo "<p>Không có lịch sử truy cập.</p>";
} else {
    echo "<table>
            <tr>
                <th>STT</th>
                <th>Tài khoản</th>
                <th>Hành động</th>
                <th>IP</th>
                <th>Trình duyệt</th>
                <th>Thời gian</th>
            </tr>";
    $i = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>$i</td>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['action']) . "</td>
                <td>{$row['ip_address']}</td>
                <td>" . htmlspecialchars($row['user_agent']) . "</td>
                <td>{$row['created_at']}</td>
              </tr>";
        $i++;
    }
    echo "</table>";
}
?>

</body>
</html>
