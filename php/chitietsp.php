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

    $idsp = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $product = null;

    if ($idsp > 0) {
        $stmt = $conn->prepare("SELECT tensp, hinhanh, giasp, MoTa FROM sanpham WHERE idsp = ?");
        $stmt->bind_param("i", $idsp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
        }
        $stmt->close();
    }
    $stmt = $conn->prepare("SELECT tennguoidanhgia, sosao, noidung, ngaydanhgia FROM reviews WHERE product_id = ? ORDER BY ngaydanhgia DESC");
      $stmt->bind_param("i", $idsp);
      $stmt->execute();
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()) {
          $reviews[] = $row;
      }
      $stmt->close();
      
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $quantity = max($quantity, 1); // Đảm bảo số lượng tối thiểu là 1

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'increase') {
                $quantity++;
            } elseif ($_POST['action'] === 'decrease') {
                $quantity = max($quantity - 1, 1);
            }
        }

        if (isset($_POST['add_to_cart'])) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (!isset($_SESSION['cart'][$idsp])) {
                $_SESSION['cart'][$idsp] = $quantity;
            } else {
                $_SESSION['cart'][$idsp] += $quantity;
            }

            header("Location: giohang.php");
            exit();
        }
    }
    $conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm - <?php echo htmlspecialchars($product['tensp']); ?></title>
    <style>
                * {
            margin: 4px;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f8f8;
            display: flex;
            justify-content: center;
        }

        .container {
            background-color: #fff;
            width: 100%;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 170px;
        }

        .product-gallery-and-details {
            display: flex;
            gap: 20px;
            width: 100%;
        }

        .product-gallery {
            width: 60%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .product-gallery .main-image {
            width: 100%;
            max-width: 500px;
            border-radius: 10px;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .product-gallery .main-image:hover {
            transform: scale(1.05);
        }

        .thumbnail-row {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .thumbnail-row img {
            width: 60px;
            height: 60px;
            cursor: pointer;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .thumbnail-row img:hover {
            border-color: #3498db;
        }

        .product-details {
            width: 40%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .product-details h1 {
            font-size: 32px;
            color: #333;
        }

        .product-details .reviews {
            color: #f39c12;
            font-size: 14px;
        }

        .product-details .price {
            font-size: 28px;
            color: #e74c3c;
            font-weight: bold;
        }

        .product-details .short-description {
            line-height: 1.6;
            color: #666;
            width: 1100px;
        }

        .product-details .actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-details .actions input {
            width: 50px;
            text-align: center;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .product-details .button {
            padding: 10px 20px;
            background-color: #979206;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .product-details .button:hover {
            background-color: #979206;
        }

        .availability {
            margin-top: 20px;
            font-size: 14px;
        }

        .availability span {
            font-weight: bold;
            color: #3498db;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-icons a {
            text-decoration: none;
            color: #666;
            font-size: 18px;
        }
        .reviews-section {
        margin-top: 30px;
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .reviews-section h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .review-item {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-item h3 {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .review-stars {
            color: #f39c12;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .review-item p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .review-item span {
            font-size: 14px;
            color: #777;
        }

        .social-icons {
            margin-top: 20px;
            display: flex;
            gap: 15px;
        }

        .social-icons a {
            text-decoration: none;
            color: #555;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: #3498db;
        }
        .product-details .actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-details .actions button {
            width: 30px;
            height: 30px;
            background-color:white; 
            color: black;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .product-details .actions button:hover {
            background-color: #2c81ba;
        }

        .product-details .actions input {
            width: 50px;
            text-align: center;
            font-size: 16px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .product-details .actions button[name="add_to_cart"] {
            height: 40px;
            width: auto;
            padding: 8px 15px;
            background-color: #555555;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            color: white;
        }

        .product-details .actions button[name="add_to_cart"]:hover {
            background-color: #E7717D;
        }
    </style>
</head>
<body>
<?php include 'header2.php'; ?>
<?php if ($product): ?>
<div class="container">
    <div class="product-gallery-and-details">
        <div class="product-gallery">
            <img class="main-image" src="<?php echo htmlspecialchars($product['hinhanh']); ?>" alt="<?php echo htmlspecialchars($product['tensp']); ?>">
            <div class="thumbnail-row">
                <img src="<?php echo htmlspecialchars($product['hinhanh']); ?>" alt="Thumbnail 1">
                <img src="<?php echo htmlspecialchars($product['hinhanh']); ?>" alt="Thumbnail 2">
                <img src="<?php echo htmlspecialchars($product['hinhanh']); ?>" alt="Thumbnail 3">
            </div>
        </div>
        <div class="product-details">
            <h1><?php echo htmlspecialchars($product['tensp']); ?></h1>
            <div class="reviews">★★★★★ (999 đánh giá)</div>

            <div class="availability">
                <p>Cam kết: <span>Hàng chính hãng, đảm bảo chất lượng</span></p>
                <p>Bảo hành: <span>6 tháng</span></p>
                <p>Trạng thái: <span>Còn hàng</span></p>
                <p>Vận chuyển: <span>Miễn phí</span></p>
            </div>
            <p class="price"><?php echo number_format($product['giasp'], 0, ',', '.'); ?> VND</p>
            <div class="actions">
                <form method="POST">
                    <button type="submit" name="action" value="decrease">-</button>
                    <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="1" readonly>
                    <button type="submit" name="action" value="increase">+</button>
                    <button type="submit" name="add_to_cart">Thêm vào giỏ hàng</button>
                </form>
            </div>
        </div>
    </div>
    <div class="product-details">
        <h2>Thông tin chi tiết</h2>
        <p class="short-description">
            <?php echo nl2br(htmlspecialchars($product['MoTa'])); ?>
        </p>       
    </div>
    <div class="reviews-section">
        <h2>Đánh giá sản phẩm</h2>
        <?php if (!empty($reviews)): ?>
    <?php foreach ($reviews as $review): ?>
        <div class="review-item">
            <h3><?php echo htmlspecialchars($review['tennguoidanhgia']); ?></h3>
            <div class="review-stars">
                <?php 
                    $stars = str_repeat('★', $review['sosao']) . str_repeat('☆', 5 - $review['sosao']); 
                    echo $stars;
                ?>
            </div>
            <p><?php echo nl2br(htmlspecialchars($review['noidung'])); ?></p>
            <span><?php echo date("d/m/Y", strtotime($review['ngaydanhgia'])); ?></span>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
    <?php endif; ?>
	<div class="social-icons">
            <a href="https://web.facebook.com/">Facebook</a>
            <a href="https://www.youtube.com/">YouTube</a>
            <a href="https://www.instagram.com/">Instagram</a>
    </div>

</div>
<?php else: ?>
    <p>Không tìm thấy sản phẩm.</p>
<?php endif; ?>
<?php include 'footer.php'; ?>
</body>
</html>
