<?php
session_start();

    $success = ""; 
    $errors = [];
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "ql_webbanhang";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Kết nối thất bại: " . $conn->connect_error);
        }
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $content = trim($_POST['content']);

        if (empty($name)) {
            $errors[] = "Vui lòng nhập họ và tên.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không hợp lệ.";
        }
        if (empty($phone) || !preg_match("/^[0-9]{10}$/", $phone)) {
            $errors[] = "Số điện thoại không hợp lệ. Vui lòng nhập 10 chữ số.";
        }
        if (empty($content)) {
            $errors[] = "Vui lòng nhập nội dung tin nhắn.";
        }

        if (empty($errors)) {
            $sql = "INSERT INTO contact (name, email, phone, content) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $email, $phone, $content);

            if ($stmt->execute()) {
                $success = "Liên hệ đã được gửi thành công!";
            } else {
                $errors[] = "Lỗi: " . $stmt->error;
            }

            $stmt->close();
        }

        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Liên Hệ</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #e1d9d9;
        }

        .containerr {
            display: flex;
            max-width: 1000px;
            width: 100%;
            justify-content: center;
            align-items: stretch; 
            gap: 20px;
            margin: 0 auto;
            padding-top: 170px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .left-section, .right-section {
            padding: 42px;
            width: 50%;
            display: flex;
            flex-direction: column; 
            justify-content: space-between;
        }
        .right-section{
            margin-top: 10px;
        }
        .right-section h2 {
            margin-top: -9px;
        }

        .left-section h2, .right-section h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #0057a3;
            text-align: center;
            min-height: 40px; 
        }

        .left-section p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            text-align: justify;
        }

        .right-section p {
            margin-top: -15px;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            text-align: justify;
        }

        .contact-info div {
            margin-bottom: 15px;
        }

        .contact-info h3 {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .contact-info p {
            font-size: 13px;
            color: #555;
        }

        .right-section p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 40px;
            margin-top: 25px;
        }

        form label {
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        form input, form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .send button {
            width: 100%;
            padding: 12px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #28a745; 
            border: none;
            border-radius: 6px; 
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            transition: background-color 0.3s ease, transform 0.2s ease; 
        }

        .send button:hover {
            background-color: #218838;
        }

        .send button:active {
            background-color: #1e7e34; 
            transform: translateY(0); 
        }
        .success-message {
            font-size: 14px;
            color: green; 
            margin-bottom: 15px;
        }

        .error-message {
            font-size: 14px;
            color: red; 
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<?php include 'header2.php'; ?>
<div class="containerr">
        <div class="left-section">
            <h2>LIÊN HỆ</h2>
            <p>
                Kính Chào Quý Khách! Với đội ngũ chuyên nghiệp, cùng sự làm việc rất tâm huyết và chăm chỉ để mang lại sự hài lòng. 
                Đặc biệt hơn nữa là đem lại những trải nghiệm tuyệt vời tới anh/chị khi mua sắm trên Website bán đồ gia dung của chúng tôi. 
                Hãy để lại thông tin hoặc liên hệ để được tư vấn Miễn Phí nhé !
            </p>
            <div class="contact-info">
                <div>
                    <h3>Địa chỉ</h3>
                    <p> * Đường Lĩnh Nam<br>
                        * Quận Hoàng Mai<br>
                        * Thành phố Hà Nội</p>
                </div>
                <div>
                    <h3>Thời gian làm việc</h3>
                    <p>* Thứ 2 - 7 : 09:00 - 17:00<br>
                       * Chủ Nhật : 08:00 - 12:00</p>
                </div>
                <div>
                    <h3>Email</h3>
                    <p>* huytung@gmail.com<br>
                       * thuyquynh@gmail.com<br>
                       * vantoan@gmail.com</p>
                </div>
                <div>
                    <h3>Số điện thoại</h3>
                    <p>* 0898 524 365<br>
                       * 0898 524 366<br>
                       * 0898 524 367</p>
                </div>
            </div>
        </div>
        <div class="right-section">
            <h2>ĐỂ LẠI THÔNG TIN TƯ VẤN</h2>
            <p>Hãy điền thông tin và gửi tới chuyên gia tư vấn nhiều kinh nghiệm sẽ tư vấn giúp bạn về vấn đề đang gặp phải.</p>
            <form action="#" method="post">
                <label for="name">Họ & Tên (*)</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email (*)</label>
                <input type="email" id="email" name="email" required>

                <label for="phone">Số điện thoại (*)</label>
                <input type="phone" id="phone" name="phone" required>

                <label for="content">Để lại lời nhắn cho chúng tôi</label>
                <textarea id="content" name="content" rows="4"></textarea>

                    <?php if (!empty($success)) : ?>
                    <p class="success-message"><?php echo $success; ?></p>
                <?php endif; ?>

                <?php if (!empty($errors)) : ?>
                    <?php foreach ($errors as $error) : ?>
                        <p class="error-message"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="send"><button type="submit">Gửi liên hệ</button></div>
            </form>
        </div>
    </div>
    <?php include 'back.php'; ?>
    <?php include 'footer.php'; ?>
</body>

</html>
