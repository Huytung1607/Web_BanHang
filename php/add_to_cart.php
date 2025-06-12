<?php
    session_start();
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
    header("Location: giohang.php");
    exit;
?>
