<?php
session_start();
$conn = new mysqli("localhost", "root", "", "library");
if ($conn->connect_error) die("Lỗi DB");

// Giả lập Thủ thư đăng nhập
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'librarian';

// Xử lý duyệt/từ chối
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['reservation_id'];
    $action = $_POST['action'];
    $reason = $_POST['reason'] ?? null;

    if ($_SESSION['role'] !== 'librarian') {
        echo "<script>alert('Bạn không có quyền.');</script>";
    } else {
        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE reservations SET status='approved', reason=NULL WHERE id=?");
            $stmt->bind_param("i", $id);
        } elseif ($action === 'reject') {
            if (empty(trim($reason))) {
                echo "<script>alert('Vui lòng nhập lý do từ chối');</script>";
                goto BANG;
            }
            $stmt = $conn->prepare("UPDATE reservations SET status='rejected', reason=? WHERE id=?");
            $stmt->bind_param("si", $reason, $id);
        }
        if ($stmt->execute()) {
            echo "<script>alert('Xử lý thành công!');</script>";
        }
    }
}

BANG:
// Lấy danh sách yêu cầu chờ duyệt
$res = $conn->query("
    SELECT r.id, u.username, r.book_title, r.created_at
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    WHERE r.status = 'pending'
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Duyệt yêu cầu đặt sách</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #ccc; padding: 8px; }
        th { background: #f2f2f2; }
        textarea { width: 100%; height: 60px; }
    </style>
</head>
<body>
<h2>📚 Duyệt yêu cầu đặt trước sách</h2>
<?php if ($res->num_rows === 0): ?>
    <p>Không có yêu cầu nào cần xử lý.</p>
<?php else: ?>
    <table>
        <tr>
            <th>STT</th>
            <th>Người dùng</th>
            <th>Sách</th>
            <th>Thời gian</th>
            <th>Thao tác</th>
        </tr>
        <?php $i = 1; while ($row = $res->fetch_assoc()): ?>
        <tr>
            <form method="post">
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['book_title']) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <input type="hidden" name="reservation_id" value="<?= $row['id'] ?>">
                    <button name="action" value="approve">✅ Duyệt</button>
                    <button name="action" value="reject" onclick="return confirm('Bạn chắc chắn muốn từ chối?')">❌ Từ chối</button><br>
                    <textarea name="reason" placeholder="Lý do từ chối (nếu có)..."></textarea>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>
</body>
</html>
