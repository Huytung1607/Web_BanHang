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

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;

$sql = "SELECT * FROM sanpham WHERE tensp LIKE ?";
$params = ["%$keyword%"];

if (!empty($category) && $category != "Tất cả") {
    $sql .= " AND danhmuc = ?";
    $params[] = $category;
}

if ($min_price > 0) {
    $sql .= " AND giasp >= ?";
    $params[] = $min_price;
}

if ($max_price > 0) {
    $sql .= " AND giasp <= ?";
    $params[] = $max_price;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("s", count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm</title>
    <style>
    .container {
        display: flex;
        margin-top: 20px;
    }

    .sidebar {
        width: 300px;
        padding: 20px;
        background-color: #f0f0f5;
        border-right: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    .sidebar h3 {
        font-size: 20px;
        color: #333;
        margin-bottom: 15px;
        text-align: center;
    }

    .sidebar form {
        display: flex;
        flex-direction: column;
    }

    .sidebar form label {
        font-size: 14px;
        color: #555;
        margin-bottom: 5px;
        margin-top: 10px;
    }

    .sidebar form input[type="text"],
    .sidebar form input[type="number"],
    .sidebar form select {
        padding: 8px;
        font-size: 14px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        outline: none;
        transition: border-color 0.3s ease;
    }

    .sidebar form input[type="text"]:focus,
    .sidebar form input[type="number"]:focus,
    .sidebar form select:focus {
        border-color: #E7717D;
    }

    .sidebar form button {
        padding: 10px;
        background-color: #555555;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    .sidebar form button:hover {
        background-color: #d4606d;
    }
    .products-container {
        display: flex;
        flex-wrap: wrap; 
        gap: 20px;  
        flex: 1;  
    }
    .product {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: calc(25% - 20px);  
        margin-bottom: 20px;
        text-align: center;
        padding: 15px;
        transition: transform 0.3s ease-in-out;
        box-sizing: border-box;  
    }

    .product:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        transition: transform 0.3s ease-in-out;
    }

    .product-image:hover {
        transform: scale(1.1);
    }

    .product-name {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin: 10px 0;
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
        padding: 10px;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s ease;
    }

    .add-to-cart:hover {
        background-color: #D85C6E;
    }

    .add-to-cart:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    .product a {
        display: block;
        text-decoration: none;
    }

    </style>
</head>
<body>
    <?php include 'header2.php'; ?>
    <?php include 'banner.php'; ?>

    <div class="container">
    <div class="sidebar">
        <h3>Tìm kiếm nâng cao</h3>
        <form action="" method="get">
            <label for="keyword">Từ khóa</label>
            <input type="text" name="keyword" id="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
            
            <label for="category">Danh mục</label>
            <select name="category" id="category">
            <option value="Tất cả" <?php if ($category == "Tất cả") echo "selected"; ?>>Tất cả</option>
            <option value="kitchen" <?php if ($category == "kitchen") echo "selected"; ?>>Thiết bị nhà bếp</option>
            <option value="cleaning" <?php if ($category == "cleaning") echo "selected"; ?>>Thiết bị vệ sinh</option>
            <option value="climate" <?php if ($category == "climate") echo "selected"; ?>>Thiết bị điện lạnh</option>
            <option value="furniture" <?php if ($category == "furniture") echo "selected"; ?>>Đồ nội thất</option>
            <option value="other" <?php if ($category == "other") echo "selected"; ?>>Khác</option>
            </select>

            <label for="min_price">Giá tối thiểu</label>
            <input type="number" name="min_price" id="min_price" value="<?php echo $min_price; ?>" min="0">

            <label for="max_price">Giá tối đa</label>
            <input type="number" name="max_price" id="max_price" value="<?php echo $max_price; ?>" min="0">

            <button type="submit">Tìm kiếm</button>
        </form>
    </div>

    <div class="products-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $productDetailUrl = "chitietsp.php?id=" . $row["idsp"];           
                echo "<div class='product'>
                        <a href='" . $productDetailUrl . "'>
                            <img src='" . htmlspecialchars($row["hinhanh"]) . "' alt='" . htmlspecialchars($row["tensp"]) . "' class='product-image'>
                        </a>
                        <p class='product-name'>" . htmlspecialchars($row["tensp"]) . "</p>
                        <p class='product-price'>Giá: " . number_format($row["giasp"], 0, ',', '.') . " VND</p>
                        <button class='add-to-cart' onclick=\"addToCart(" . $row["idsp"] . ")\">Thêm vào giỏ hàng</button>
                      </div>";
            }
        } else {
            echo "<p>Không có sản phẩm nào trong danh mục này.</p>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
</div>


<?php include 'footer.php'; ?>
</body>
</html>
