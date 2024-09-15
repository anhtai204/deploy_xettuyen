<?php 
    include './layouts/header.php';
?>
<?php
    $m=0;
    $n=0;
    $token = md5(uniqid());
?>

<?php 
    // nếu chưa đăng nhập thì out
    $sql = "SELECT * FROM admins;";
    $res = mysqli_query($connect, $sql);
    $query_username = mysqli_fetch_array($res);
    $username_admin = $query_username['username'];
    if($_SESSION['taiKhoan'] != $username_admin){
        header("location: logout.php");
    }
?>


<?php 
    $s1 = "SELECT * FROM subject_combination;";
    $query_combine = mysqli_query($connect, $s1);

    $combines = array();

    while($row=mysqli_fetch_array($query_combine)){
        array_push($combines, $row['id_SB']);
        // echo $row['id_SB'] . '-' . $row['sub_1'] . '-' . $row['sub_2'] . '-' . $row['sub_3'] . '<br>';
    }
?>

<?php 
    $s2 = "SELECT * FROM chuyennganh;";
    $query_major = mysqli_query($connect, $s2);

    $majors = array();

    while($row=mysqli_fetch_array($query_major)){
        array_push($majors, $row['ten_chuyen_nganh']);
        // echo $row['id_SB'] . '-' . $row['sub_1'] . '-' . $row['sub_2'] . '-' . $row['sub_3'] . '<br>';
    }
?>

<?php
    $j = 0;
    $chuyennganh_array = isset($_SESSION['chuyennganh_array']) ? $_SESSION['chuyennganh_array'] : array();
    $tohop_array = isset($_SESSION['tohop_array']) ? $_SESSION['tohop_array'] : array();
    // tạo thêm 1 array để lưu status của chuyennganh
    $status_chuyennganh = isset($_SESSION['status_chuyennganh']) ? $_SESSION['status_chuyennganh'] : array();

    // Xử lí phần thêm dữ liệu vào bảng khi bấm submit
    if (isset($_POST['them']) && $_SESSION['token'] == $_POST['_token']) {
        $chuyenNganh = $_POST['chuyennganh'];
        $toHop = $_POST['tohop'];
        if($chuyenNganh =='' || $toHop == ''){
           // 
        } else {
            // Nếu chuyenNganh đã tồn tại trong mảng thì lấy giá trị hiện tại ra, nếu không thì tạo mới
            if (!isset($chuyennganh_array[$chuyenNganh])) {
                $chuyennganh_array[$chuyenNganh] = array();
            }
    
            // Thêm toHop vào mảng của chuyenNganh
            if(!in_array($toHop, $chuyennganh_array[$chuyenNganh])){
                array_push($chuyennganh_array[$chuyenNganh], $toHop);
            }
    
            // set status chuyenNganh 0-hủy, 1-chưa duyệt, 2-đã duyệt
            $status_chuyennganh[$chuyenNganh] = 1;
            
            // Cập nhật session
            $_SESSION['chuyennganh_array'] = $chuyennganh_array;
            $_SESSION['status_chuyennganh'] = $status_chuyennganh;
        }
    }

    // print_r($_SESSION['chuyennganh_array']);
    // echo '<br>';
    // print_r($_SESSION['status_chuyennganh']);

    
?>

<?php
    // xử lí phần access, delete
    if(isset($_POST['access']) && $_SESSION['token'] == $_POST['_token']){
        $chuyennganh_array = $_SESSION['chuyennganh_array'];
        $chuyennganh_at_row = $_POST['row_id'];

        // echo $row_id;
        // print_r($chuyennganh_array[$row_id]);
        $string_tohop = implode(" - ", $chuyennganh_array[$chuyennganh_at_row]);
        // echo $string_tohop;

        $s1 = "INSERT INTO majors(major, subject_combination_id_list) VALUES ('$chuyennganh_at_row', '$string_tohop');";
        mysqli_query($connect, $s1);

        $_SESSION['status_chuyennganh'][$chuyennganh_at_row] = 2;

        // reload lại trang
        header('Location: admin_tao_ho_so.php'); // Đúng cách
        exit(); // Kết thúc script sau khi chuyển hướng
    }

    if(isset($_POST['delete'])){
        $chuyennganh_array = $_SESSION['chuyennganh_array'];
        $chuyennganh_at_row = $_POST['row_id'];

        $_SESSION['status_chuyennganh'][$chuyennganh_at_row] = 0;

        // reload lại trang
        header('Location: admin_tao_ho_so.php'); 
        exit();
    }
?>

<?php
    // kiểm tra xem có chuyên ngành nào chưa duyệt không
    $count_status = 0;
    if(isset($_SESSION['status_chuyennganh'])){
        $status_chuyennganh = $_SESSION['status_chuyennganh'];
        foreach($status_chuyennganh as $chuyenNganh => $status){
            if($status == 1){
                $count_status += 1;
            }
        }
        // echo $count_status;
    };
?>


<div style="display: flex; justify-content: center;">

<?php 
    if(isset($_SESSION['role'])) {
		include './layouts/menu.php';
	}
?>


<div class="container" style="display: block; width: 100%;">
	
	<!-- Page content -->
    <form action="" method="post">
        <div class="container_head">
            <div id="majors">
                <div class="title-header" style="font-weight: bold; font-size: 20px;">Chuyên ngành xét tuyển</div>
                <i class="menu-icon ti-menu-alt"></i>
                <select name="chuyennganh" id="major_list" required>
                    <option></option>
                    <?php foreach($majors as $major) { ?>
                        <?php # echo '<input type="hidden" name="row_id" value="' . $m . '">' ?>
                        <?php echo '<option  value="' . $major .'">' . $major . '</option>'?>
                        <?php } ?>
                </select><br>
                <div class="error"><?php echo isset($error_major) ? $error_major : ""?></div>
            </div>
    
            <div id="combines">
                <div class="title-header" style="font-weight: bold; font-size: 20px;">Tổ hợp xét tuyển</div>
                <i class="menu-icon ti-menu-alt"></i>
                <select name="tohop" id="major_list" required>
                    <option></option>
                    <?php foreach($combines as $combine) { ?>
                        <?php # echo '<input type="hidden" name="row_id" value="' . $n . '">' ?>
                        <?php echo '<option value="' . $combine .'">' . $combine . '</option>'?>
                        <?php } ?>
                </select>
                <div class="error"><?php echo isset($error_combine) ? $error_combine : ""?></div>
            </div>

            <div id="btn_submit">
                <input type="hidden" name="_token" value="<?php echo $token ?>">
                <?php $_SESSION['token'] = $token ?>
                <input type="submit" class="btn btn-primary" value="Thêm" name="them" id="btnAdd"/>
            </div>
        </div>
    </form>

<?php 
    if($count_status >= 1){
        echo '<div class="container_body">
                <form action="" method="post">
                    <table>
                        <th>STT</th>
                        <th>Chuyên ngành xét tuyển</th>
                        <th>Tổ hợp xét tuyển</th>
                        <th>Access</th>
                        <th>Delete</th>'
                        ?>
                        <?php $chuyennganh_array = $_SESSION['chuyennganh_array']; ?>
                        <?php
                            foreach($chuyennganh_array as $chuyenNganh => $toHop){
                                if($status_chuyennganh[$chuyenNganh] == 1){
                                    echo  '<tr id="id_' . $j . '">';
                                        echo '<td>' . $j . '</td>';
                                        echo '<td>' . $chuyenNganh . '</td>';
                                        ?>

                                        <?php 
                                        $k = implode(" - ", $toHop);
                                        echo '<td>' . $k . '</td>';

                                        echo '<td>
                                                <form action="" method="post">
                                                    <input type="hidden" name="row_id" value="' . $chuyenNganh . '">
                                                    <button type="submit" class="btn btn-primary" name="access">Click</button>
                                                    <input type="hidden" name="_token" value="'?><?php echo $token .'"/>' ?>
                                                    <?php $_SESSION['token'] = $token; ?>
                                                <?php echo '</form>
                                            </td>';
                                            echo '<td>
                                                    <form action="" method="post">
                                                        <input type="hidden" name="row_id" value="' . $chuyenNganh . '">
                                                        <button type="submit" class="btn btn-danger" name="delete">Click</button>
                                                    </form>
                                                </td>';
                                        $j+=1;
                                    echo '</tr>';
                                }
                            }
                    ?>
                    </table>
                </form>
            </div>
<?php 
    } else {
        ?>
        <h2 style="text-align: center; margin-top: 30px;">Chưa có yêu cầu duyệt chuyên ngành !</h2>
        <?php
    }
?>

	<?php 
		include './layouts/footer.php';
	?>
</div>