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

if (isset($_GET['add_to_cart'])) {
    $idsp = $_GET['add_to_cart'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (!isset($_SESSION['cart'][$idsp])) {
        $_SESSION['cart'][$idsp] = 1;
    } else {
        $_SESSION['cart'][$idsp]++;
    }

    $_SESSION['message'] = "Bạn đã thêm vào giỏ hàng thành công!";
    header("Location: giohang.php");
    exit();
}

$category = isset($_GET['category']) ? $_GET['category'] : '';
$sql = $category ? "SELECT idsp, hinhanh, tensp, giasp FROM sanpham WHERE danhmuc = '$category'" : "SELECT idsp, hinhanh, tensp, giasp FROM sanpham";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Bán Hàng Gia Dụng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
        }
        .center-menu {
            padding: 37px 20px 60px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            width: 1350px;
            justify-self: center;
            position: relative;
        }
        .product {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            background-color: #e1d9d9;
            border-radius: 8px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
    .product:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }


    .product-image:hover {
        transform: scale(1.1);
    }

        .product img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            width: 100%;
            transition: transform 0.3s ease-in-out;
        }
        .product p {
            margin: 5px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .product-price {
            font-size: 16px;
            color: #FF3366; 
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .add-to-cart {
            background-color: #555555;
            color: white;
            border: none;
            padding: 8px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .add-to-cart:hover {
            background-color: #E7717D;
        }
        .message-box {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            font-size: 18px;
            text-align: center;
        }
    </style>
    <script>
        function showMessage() {
            const messageBox = document.getElementById('messageBox');
            messageBox.style.display = 'block';
            setTimeout(() => {
                messageBox.style.display = 'none';
            }, 1000); 
        }
    </script>
</head>
<body onload="<?php if (isset($_SESSION['message'])) { echo 'showMessage();'; unset($_SESSION['message']); } ?>">
    <div class="center-menu">
        
        <div id="messageBox" class="message-box"><?= $_SESSION['message'] ?? '' ?></div>

        <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product">
            <a href="chitietsp.php?id=<?= $row['idsp']; ?>">
            <img src="<?= htmlspecialchars($row['hinhanh']); ?>" alt="<?= htmlspecialchars($row['tensp']); ?>">
            </a>
            <p><?= htmlspecialchars($row['tensp']); ?></p>
            <p class="product-price">Giá: <?= number_format($row['giasp'], 0, ',', '.') . " VND"; ?></p>
            <button class="add-to-cart" onclick="location.href='sp.php?add_to_cart=<?= $row['idsp']; ?>'">Thêm vào giỏ hàng</button>
        </div>
    <?php endwhile; ?>
<?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
