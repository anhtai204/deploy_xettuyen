<?php 
   include './layouts/header.php';
   use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/PHPMailer/src/Exception.php';
    require 'vendor/PHPMailer/src/PHPMailer.php';
    require 'vendor/PHPMailer/src/SMTP.php';
?>

<script>
    // sau 60s (thời hạn mã code là 60s) reload lại trang để kiểm tra email bị admin deny hay không
    setTimeout(function() {
        window.location.href="xacthuc.php";
    }, 60000);
</script>

<?php
    if(!isset($_SESSION['taiKhoan'])){
        ?>
        <script>
            alert("Bạn không được phép điều hướng tới trang này khi chưa đăng ký!");
            window.location.href="index.php";
        </script>
        <?php
    }

    // echo $hoVaTen . '-' . $taiKhoan . '-' . $matKhau . '-' . $gioiTinh . '-' . $ngaySinh . '-' . $diaChi . '-' . $soDienThoai . '-' . $email;
    $email = $_SESSION['email'];
    // kiểm tra nếu tài email đã được đăng kí thì yêu cầu nhập lại
    $sql1 = "SELECT * FROM teachers WHERE email='$email';";
    $sql2 = "SELECT * FROM students WHERE email='$email';";

    $result1 = mysqli_query($connect, $sql1);
    $result2 = mysqli_query($connect, $sql2);

    if(mysqli_num_rows($result1) > 0 || mysqli_num_rows($result2) > 0){
       ?>
        <script>
            alert("Email đã được đăng ký, hãy chọn email khác");
            window.location.href="dangky.php";
        </script>
       <?php
    } else {
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
    
        // echo mysqli_affected_rows($connect);
    
        // lấy ra các code còn hạn trong bảng gen_code
        $array_codes = array();
        $s2 = "SELECT code FROM gen_code WHERE NOW() <= expiry_time;";
        $query_code = mysqli_query($connect, $s2);
        while($row = mysqli_fetch_array($query_code)) {
            echo $row['code'];
            array_push($array_codes, $row['code']);
        }
    }

    

    if(isset($_POST['submit'])){
        $verify_code = $_POST['verifyCode'];
        if(in_array($verify_code, $array_codes)){
            // $b = "UPDATE guest SET status = 2 WHERE teacher_email = '$email';";
            // mysqli_query($connect, $b);

            $insert_teacher = "INSERT INTO teachers(username, password, fullname, ngaysinh, phone_number, gender, address, email)
            VALUES ('$taiKhoan', '$matKhau', '$hoVaTen', '$ngaySinh', '$soDienThoai', '$gioiTinh', '$diaChi', '$email');"; 
            mysqli_query($connect, $insert_teacher);

            ?>
            <script>
                alert("Xác minh thành công");
                window.location.href="index.php";
            </script>
            <?php
        } else {
            ?>
            <script>
                alert("Passcode không chính xác hoạt đã quá thời hạn, mời bạn quay lại trang đăng ký !");
                window.location.href="dangky.php";
            </script>
            <?php
            echo "error";
        }
    }

?>


<?php
    // kiểm tra nếu email không được chấp nhận 
    $c = "SELECT * FROM guest WHERE teacher_email='$email';";
    $query_mail = mysqli_query($connect, $c);

    $kq = mysqli_fetch_array($query_mail);
    $status_mail = $kq['status'];

    if($status_mail == 0){
        // xóa email đã gửi yêu cầu đăng ký giáo viên trong bảng guest vì có thể đăng ký lại với email đó và được admin phê duyệt
        $m = "DELETE FROM guest WHERE teacher_email='$email';";
        mysqli_query($connect, $m)
        ?>
        <script>
            alert("Yêu cầu đăng ký tài khoản giáo viên của bạn không được chấp nhận !");
            window.location.href="index.php";
        </script>
        <?php
    }
?>




<h1 style="text-align: center;"> Tài khoản của bạn đang chờ được duyệt </h1>

<div class="container">
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <div>Nhập mã xác thực đã gửi tới Gmail</div>
        <input type="text" name="verifyCode">
        <input type="submit" value="Xác thực" name="submit">
    </form>
</div>


<?php 
   include './layouts/footer.php';
?>