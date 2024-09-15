<?php 
    include './layouts/header.php';
?>
<pre>
<?php 
    if(isset($_POST['submit'])) {
        $taiKhoan = $_POST['taiKhoan'];
        $matKhau = $_POST['matKhau'];
        $matKhau_hashed = md5($matKhau);

        if($taiKhoan == "" || $matKhau == "") {
            echo("<script>alert('Vui lòng nhập đầy đủ tài khoản và mật khẩu!')</script>");
        } else {
            // $sql = "SELECT * FROM admins WHERE username ='$taiKhoan' AND password ='$matKhau_hashed'";
            $sql_admins = "SELECT * FROM admins WHERE username ='$taiKhoan' AND password ='$matKhau_hashed'";
            $sql_teachers = "SELECT * FROM teachers WHERE username ='$taiKhoan' AND password ='$matKhau_hashed'";
            $sql_students = "SELECT * FROM students WHERE username ='$taiKhoan' AND password ='$matKhau_hashed'";

            $query_admins = mysqli_query($connect, $sql_admins);
            $query_teachers = mysqli_query($connect, $sql_teachers);
            $query_students = mysqli_query($connect, $sql_students);
    
            $result_admins = mysqli_num_rows($query_admins);
            $result_teachers = mysqli_num_rows($query_teachers);
            $result_students = mysqli_num_rows($query_students);

            $check = true;
            if($result_admins == 1) {
                $_SESSION['role'] = "admin";
                $arr = mysqli_fetch_array($query_admins);
            } elseif($result_teachers == 1) {
                $_SESSION['role'] = "teacher";
                $arr = mysqli_fetch_array($query_teachers);
            } elseif($result_students == 1) {
                $_SESSION['role'] = "student";
                $arr = mysqli_fetch_array($query_students);
            } else {
                $check = false;
            }
            
            if($check) {
                $_SESSION['hoVaTen'] = $arr['fullname'];
                $_SESSION['taiKhoan'] = $arr['username'];
                $_SESSION['matKhau'] = $arr['password'];
                $_SESSION['gioiTinh'] = $arr['gender'];
                $_SESSION['ngaySinh'] = $arr['ngaysinh'];
                $_SESSION['diaChi'] = $arr['address'];
                $_SESSION['soDienThoai'] = $arr['phone_number'];
                $_SESSION['email'] = $arr['email'];
                $_SESSION['menu_status'] = "close";
    
                header("location: index.php");
            } else {
                echo("<script>alert('Tài khoản hoặc mật khẩu chưa đúng!')</script>");
            }
        }
        echo "<meta http-equiv='refresh' content='0'>";     #reload trang không gửi request
    }
?>
</pre>

<main class="form-signin w-50 m-auto">
    <form class="text-center" action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <img src="<%=url %>/img/logo/logo.png" alt="" height="70" style="margin: 0px">
    <h1 class="h3 mb-3 fw-normal">Đăng nhập</h1>
    <div class="text-center"><span class="red"><?php $baoLoi ?></span></div>

    <div class="form-floating">
        <input type="text" class="form-control" id="taiKhoan" placeholder="Tên đăng nhập" name="taiKhoan">
        <label for="taiKhoan">Tên đăng nhập</label>
    </div>
    <div class="form-floating">
        <input type="password" class="form-control" id="matKhau" placeholder="Password" name="matKhau">
        <label for="matKhau">Mật khẩu</label>
    </div>

    <div class="form-check text-start my-3">
        <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
        Remember me
        </label>
    </div>
    <button name="submit" class="btn btn-primary w-100 py-2" type="submit">Sign in</button>
    <a href="dangky.php">Đăng ký tài khoản mới</a>
    <!-- <a href="forgot_pasword.php">Forgot passowrd</a> -->
    
    <p class="mt-5 mb-3 text-body-secondary">&copy; 2017–2024</p>
    </form>
</main>


<?php 
    include './layouts/footer.php'
?>