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

if (isset($_GET['add_to_cart'])) {
    $idsp = $_GET['add_to_cart'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$idsp])) {
        $_SESSION['cart'][$idsp]++;
    } else {
        $_SESSION['cart'][$idsp] = 1;
    }

    header("Location: home.php");
    exit();
}

$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($category) {
    $sql = "SELECT idsp, hinhanh, tensp, giasp FROM sanpham WHERE danhmuc = '$category'";
} else {
    $sql = "SELECT idsp, hinhanh, tensp, giasp FROM sanpham"; 
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website bán hàng gia dụng</title>
    <style>
        body {
            background-color: #FBEEC1;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }     
    </style>
</head>
<body>

<?php include 'header2.php'; ?>
<?php include 'banner.php'; ?>  
<?php include 'menuSale.php'; ?> 
<?php include 'back.php'; ?> 
<?php include 'footer.php'; ?>

</body>
</html>
