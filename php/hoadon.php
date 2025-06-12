<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ql_webbanhang";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$userId = isset($_SESSION['username']) ? $_SESSION['username'] : null;

$user_info = null;
if ($userId) {
    $sql_user = "SELECT accountname, email, address, phone FROM user WHERE username = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $userId); 
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user && $row_user = $result_user->fetch_assoc()) {
        $user_info = $row_user;
    }

    $stmt_user->close();
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$totalAmount = 0;

if (!empty($cart_items)) {
    $ngayTao = date('Y-m-d H:i:s');
    $stmt_hoadon = $conn->prepare("INSERT INTO hoadon (idhd, ngaytao, tongtien) VALUES (NULL, ?, ?)");
    $stmt_hoadon->bind_param("sd", $ngayTao, $totalAmount);
    $stmt_hoadon->execute();
    $idhd = $conn->insert_id; 

    $stmt_chitiet = $conn->prepare("INSERT INTO chitiethoadon (idhd, idsp, soluong, dongia, tongtien) VALUES (?, ?, ?, ?, ?)");

    foreach ($cart_items as $idsp => $quantity) {
        $sql = "SELECT giasp FROM sanpham WHERE idsp = $idsp";
        $result = $conn->query($sql);

        if ($result && $row = $result->fetch_assoc()) {
            $dongia = $row['giasp'];
            $tongtien = $dongia * $quantity;
            $totalAmount += $tongtien;
            $stmt_chitiet->bind_param("iiidd", $idhd, $idsp, $quantity, $dongia, $tongtien);
            $stmt_chitiet->execute();
        }
    }

    $update_sql = "UPDATE hoadon SET tongtien = $totalAmount WHERE idhd = $idhd";
    $conn->query($update_sql);
    $stmt_chitiet->close();
    $stmt_hoadon->close();
    $_SESSION['payment_success'] = true;
    unset($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOÁ ĐƠN THANH TOÁN</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
        }
        .invoice-container {
             width: 210mm; 
             min-height: 297mm; 
             padding: 20mm; 
             margin: auto; 
             background: white; 
             box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); 
        }
        h1 { 
            text-align: center; 
            font-size: 24px; 
            margin-bottom: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            padding: 10px; 
            border: 1px solid #ddd; 
            text-align: left; 
        }
        .total { 
            text-align: right; 
            font-weight: bold; 
            font-size: 18px; 
            margin-top: 20px; 
        }
        .print-btn { 
            margin-top: 20px; 
            text-align: center; 
        }
        .print-btn button { 
            background-color: #28a745; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 5px; 
            font-size: 16px; 
            cursor: pointer; 
        }
        .print-btn button:hover { 
            background-color: #218838; 
        }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>

<div class="invoice-container">
        <?php 
        if (isset($_SESSION['payment_success']) && $_SESSION['payment_success'] === true): 
            unset($_SESSION['payment_success']);
        ?>
            <div style="background-color: #28a745; color: white; padding: 10px; text-align: center; margin-bottom: 20px;">
                <strong>Đã thanh toán!</strong>
            </div>
        <?php endif; ?>

    <h1>HOÁ ĐƠN THANH TOÁN</h1>

    <?php if ($user_info): ?>
        <p><strong>Thông tin khách hàng:</strong></p>
        <p>Họ và tên: <?php echo htmlspecialchars($user_info['accountname']); ?></p>     
        <p>Email: <?php echo htmlspecialchars($user_info['email']); ?></p>
        <p>Địa chỉ: <?php echo htmlspecialchars($user_info['address']); ?></p>
        <p>Điện thoại: <?php echo htmlspecialchars($user_info['phone']); ?></p>
    <?php endif; ?>

    <?php if (!empty($idhd)): ?>
        <table>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Tổng cộng</th>
            </tr>
            <?php
            $grandTotal = 0;
            $sql = "SELECT sp.tensp, cthd.soluong, cthd.dongia, cthd.tongtien 
                    FROM chitiethoadon cthd
                    JOIN sanpham sp ON cthd.idsp = sp.idsp
                    WHERE cthd.idhd = $idhd";
            $result = $conn->query($sql);

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $grandTotal += $row['tongtien'];
                    ?>
                    <tr>
                        <td><?php echo $row['tensp']; ?></td>
                        <td><?php echo $row['soluong']; ?></td>
                        <td><?php echo number_format($row['dongia'], 0, ',', '.'); ?> VND</td>
                        <td><?php echo number_format($row['tongtien'], 0, ',', '.'); ?> VND</td>
                    </tr>
                <?php }
            }
            ?>
        </table>
        <p class="total">Tổng tiền: <?php echo number_format($grandTotal, 0, ',', '.'); ?> VND</p>
        <div class="print-btn">
            <button onclick="window.print()">In hoá đơn</button>
            <a href="trangchu.php" style="text-decoration: none;">
                <button style=" margin-left: 10px;">Quay về trang chủ</button>
            </a>
        </div>
    <?php else: ?>
        <p>Hóa đơn trống hoặc giỏ hàng đã được xử lý.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php 
$conn->close();
?>
