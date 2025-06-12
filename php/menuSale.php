<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "ql_webbanhang";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$categories = ['hangmoi', 'hangbanchay', 'hanggiamgia'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sản phẩm  - Siêu Sale 12.12</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0;
    }

    .product-container {
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .product-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
        justify-items: center;
        padding: 20px;
    }

    .product-item {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        width: 100%;
        max-width: 300px;
        padding: 8px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 372px; 
    }

    .product-item:hover {
        transform: scale(1.03);
    }

    .product-item img {
        width: 100%;
        border-radius: 6px;
        margin-bottom: 8px;
        transition: transform 0.2s ease;
        height: 170px; 
        object-fit: cover;
    }

    .product-item:hover img {
        transform: scale(1.03);
    }

    @media (max-width: 768px) {
        .product-list {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
    }

    .product-info {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-info h3 {
        font-size: 14px;
        color: #333;
        margin-bottom: 4px;
        line-height: 1.2;
    }

    .specs {
        font-size: 12px;
        color: #666;
        margin-bottom: 4px;
    }

    .price {
        margin-bottom: 6px;
    }

    .new-price {
        color: #e74c3c;
        font-size: 16px;
        font-weight: bold;
    }

    .old-price {
        color: #aaa;
        font-size: 12px;
        text-decoration: line-through;
    }

    .rating {
        font-size: 12px;
        color: #f39c12;
        margin-bottom: 4px;
    }

    .add-to-cart {
        background-color: #555;
        color: white;
        border: none;
        padding: 6px 15px;
        cursor: pointer;
        border-radius: 4px;
        font-size: 13px;
        transition: background-color 0.3s ease;
    }

    .add-to-cart:hover {
        background-color: #E7717D;
    }
</style>
</head>
<body>

<?php
foreach ($categories as $category) {
    echo "<div class='product-container'>";
    if ($category == 'hangmoi') {
        echo "<h2>* Hàng Mới</h2>";
    } elseif ($category == 'hangbanchay') {
        echo "<h2>** Hàng Bán Chạy</h2>";
    } elseif ($category == 'hanggiamgia') {
        echo "<h2>*** Hàng Giảm Giá</h2>";
    }
    echo "<div class='product-list'>";

    $sql = "SELECT * FROM sanpham WHERE loai = '$category' LIMIT 5";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='product-item'>";  
            if ($category == 'hàng mới') {
                echo "<div class='new-tag'>NEW</div>";
            } elseif ($category == 'hàng bán chạy') {
                echo "<div class='banchay-tag'>BÁN CHẠY</div>";
            } elseif ($category == 'giảm giá') {
                echo "<div class='giamgia-tag'>GIẢM GIÁ</div>";
            }
            echo "<a href='chitietsp.php?id=" . $row['idsp'] . "'><img src='" . $row['hinhanh'] . "' alt='" . $row['tensp'] . "'></a>";
            echo "<div class='product-info'>";
            echo "<h3>" . $row['tensp'] . "</h3>";
            echo "<div class='price'>";
            if ($row['giasp'] < 200000) {
                echo "<p class='new-price low-price'>" . number_format($row['giasp'], 0, ',', '.') . " đ</p>";
            } else {
                echo "<p class='new-price'>" . number_format($row['giasp'], 0, ',', '.') . " đ</p>";
            }
            echo "<p class='old-price'>" . number_format($row['giacu'], 0, ',', '.') . " đ</p>";
            echo "</div>";
            echo "<p class='rating'>★★★★★ (x đánh giá)</p>";
            echo "<button class='add-to-cart' onclick=\"location.href='sp.php?add_to_cart=" . $row['idsp'] . "'\">Thêm vào giỏ hàng</button>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>Không có sản phẩm nào trong loại này.</p>";
    }

    echo "</div>";
    echo "</div>"; 
}

// Đóng kết nối
$conn->close();
?>
</body>
</html>
