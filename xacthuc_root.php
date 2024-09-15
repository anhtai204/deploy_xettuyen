<?php 
   include './layouts/header.php';
   use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/PHPMailer/src/Exception.php';
    require 'vendor/PHPMailer/src/PHPMailer.php';
    require 'vendor/PHPMailer/src/SMTP.php';
?>

<?php
    // echo $hoVaTen . '-' . $taiKhoan . '-' . $matKhau . '-' . $gioiTinh . '-' . $ngaySinh . '-' . $diaChi . '-' . $soDienThoai . '-' . $email;
    $email = $_SESSION['email'];
    $sql = "SELECT * FROM teachers WHERE email='$email';";

    $result = mysqli_query($connect, $sql);
    if(mysqli_num_rows($result) > 0){
       ?>
        <script>
            alert("Email đã được đăng ký, hãy chọn email khác");
            // setTimeout(function() {
                window.location.href="dangky.php";
            // }, 3000);
        </script>
       <?php
    } else if(mysqli_num_rows($result) == 0) {
        // header("location: xacthuc.php");
    }
?>


<?php 

    $hoVaTen = $_SESSION['hoVaTen'];
    $taiKhoan = $_SESSION['taiKhoan'];
    $matKhau = $_SESSION['matKhau'];
    $gioiTinh = $_SESSION['gioiTinh'];
    $ngaySinh = $_SESSION['ngaySinh'];
    $diaChi = $_SESSION['diaChi'];
    $soDienThoai = $_SESSION['soDienThoai'];
    $email = $_SESSION['email'];

    // 0-huy, 1-chua duyet, 2-duyet
    $s1 = "INSERT INTO guest(teacher_email, status) VALUES ('$email', 1);";
    try {
        $result = mysqli_query($connect, $s1);
    } catch(Exception $e) {
        echo $e;
    }


    ?>
    <h1 style="text-align: center;"> Tài khoản của bạn đang chờ được duyệt </h1>
    <?php
?>

<div class="container">
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="get">
        <div>Nhập mã xác thực đã gửi tới Gmail</div>
        <input type="text" name="verifyCode">
        <input type="submit" value="Xác thực" name="submit">
    </form>
</div>


<?php 
   include './layouts/footer.php';
?>