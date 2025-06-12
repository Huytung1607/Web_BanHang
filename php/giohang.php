<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ql_webbanhang";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
        $login_error = "Vui lòng đăng nhập trước khi thanh toán.";
    }

    $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $products = [];
    if (!empty($cart_items)) {
        $ids = implode(',', array_keys($cart_items));
        $sql = "SELECT idsp, tensp, giasp, hinhanh FROM sanpham WHERE idsp IN ($ids)";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $row['quantity'] = $cart_items[$row['idsp']];
            $row['total'] = $row['giasp'] * $row['quantity'];
            $products[] = $row;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
        $productId = $_POST['product_id'];
        $action = $_POST['action'];

        if ($action == 'remove') {
            unset($cart_items[$productId]); 
        } else {
            if (isset($cart_items[$productId])) {
                if ($action == 'increase') {
                    $cart_items[$productId]++;
                } elseif ($action == 'decrease') {
                    $cart_items[$productId]--;
                    if ($cart_items[$productId] <= 0) {
                        unset($cart_items[$productId]);
                    }
                }
            }
        }
        $_SESSION['cart'] = $cart_items;
    }

    if (!empty($cart_items)) {
        $ids = implode(',', array_map('intval', array_keys($cart_items)));
        $sql = "SELECT idsp, hinhanh, tensp, giasp FROM sanpham WHERE idsp IN ($ids)";
        $result = $conn->query($sql);
    } else {
        $result = null;
    }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <style>
        /* Styles for the page */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 45px;
            margin-top: 0;
            padding-top: 200px; 
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .quantity-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .quantity-buttons button {
            width: 30px;
            height: 30px;
            font-size: 16px;
            font-weight: normal;
            border: 1px solid #ccc;
            background-color: #f2f2f2;
            cursor: pointer;
            border-radius: 4px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .quantity-buttons span {
            width: 50px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 16px;
            font-weight: normal;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
        }
        .total-cost {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
        .checkout-btn {
            display: inline-block;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
        }
        .checkout-btn:hover {
            background-color: #218838;
        }
        .error-message {
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include 'header2.php'; ?>
<div class="container">
    <h1>Giỏ Hàng Của Bạn</h1>
    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Hình ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Tổng cộng</th>
                <th>Chức năng</th>
            </tr>
            <?php
            $grandTotal = 0;
            while ($row = $result->fetch_assoc()):
                $quantity = $cart_items[$row['idsp']];
                $itemTotal = $row['giasp'] * $quantity;
                $grandTotal += $itemTotal;
            ?>
                <tr>
                    <td><img src="<?= $row['hinhanh']; ?>" alt="<?= $row['tensp']; ?>" width="60"></td>
                    <td><?= $row['tensp']; ?></td>
                    <td>
                        <form method="post" class="quantity-buttons">
                            <input type="hidden" name="product_id" value="<?= $row['idsp']; ?>">
                            <button type="submit" name="action" value="decrease">-</button>
                            <span><?= $quantity; ?></span>
                            <button type="submit" name="action" value="increase">+</button>
                        </form>
                    </td>
                    <td><?= number_format($row['giasp'], 0, ',', '.') . ' VND'; ?></td>
                    <td><?= number_format($itemTotal, 0, ',', '.') . ' VND'; ?></td>
                    <td> 
                        <form method="post">
                            <input type="hidden" name="product_id" value="<?= $row['idsp']; ?>">
                            <button type="submit" name="action" value="remove" style="background-color: #dc3545; color: white; border: none; padding: 6px 23px; border-radius: 4px; cursor: pointer;">
                                Xóa
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <div class="total-cost">Tổng tiền: <?= number_format($grandTotal, 0, ',', '.') . ' VND'; ?></div>

        <?php if (isset($login_error)): ?>
        <?php else: ?>
            <a href="hoadon.php" class="checkout-btn">Thanh Toán</a>
        <?php endif; ?>
        <?php if (isset($login_error)): ?>
        <p class="error-message"><?= $login_error; ?></p>
    <?php endif; ?>
        <a href="../php/trangchu.php" class="checkout-btn">Tiếp tục mua sắm</a>

    <?php else: ?>
        <p>Giỏ hàng của bạn đang trống.</p>
        <a href="trangchu.php" class="checkout-btn">Tiếp tục mua sắm</a>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
