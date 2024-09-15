<?php 
   include './layouts/header.php';
?>

<?php
    if(!isset($_SESSION['taiKhoan'])){
        ?>
        <script>
            alert("Bạn không được phép điều hướng tới trang này khi chưa đăng ký hoặc đăng nhập!")
            <?php session_destroy() ?>
            window.location.href="index.php";
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
        
        // kiểm tra nếu tài email đã được đăng kí thì yêu cầu nhập lại
        $sql1 = "SELECT * FROM teachers WHERE email='$email';";
        $sql2 = "SELECT * FROM students WHERE email='$email';";
    
        $result1 = mysqli_query($connect, $sql1);
        $result2 = mysqli_query($connect, $sql2);
    
        if(mysqli_num_rows($result1) > 0 || mysqli_num_rows($result2)>0){
            ?>
            <script>
                alert("Email đã được đăng ký, hãy chọn email khác");
                window.location.href="dangky.php";
            </script>
            <?php
        } else {
            $sql3 = "INSERT INTO students(username, password, fullname, ngaysinh, phone_number, gender, address, email) VALUES 
                    ('$taiKhoan', '$matKhau', '$hoVaTen', '$ngaySinh', '$soDienThoai', '$gioiTinh','$diaChi', '$email');";
            
            $result3 = mysqli_query($connect, $sql3);
        
            if($result3){
                $affected_row = mysqli_affected_rows($connect);
                if($affected_row > 0){
                    $_SESSION['taiKhoan'] = $taiKhoan;
                    ?>
                    <script>
                        alert("Đăng ký tài khoản thành công !");    
                        window.location.href="index.php";
                    </script>
                <?php 
                }
            }
        }
    
        echo $hoVaTen . '-' . $taiKhoan . '-' . $matKhau . '-' . $gioiTinh . '-' . $ngaySinh . '-' . $diaChi . '-' . $soDienThoai . '-' . $email;
    
    
    }

?>


<?php 
   include './layouts/footer.php';
?>