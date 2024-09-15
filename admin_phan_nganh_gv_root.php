<?php 
		include './layouts/header.php';
?>

<style>
	/* .form_add, .form_delete {
		display: flex;
		justify-content: space-around;
	} */

	.select_tohop {
		width: 100px;
		height: 28px;
	}

	tr td:nth-child(4){
		width: 400px;
	}
</style>

<?php 
	$token = md5(uniqid());
	$j=0;
?>


<?php 
    $s2 = "SELECT * FROM chuyennganh;";
    $query_major = mysqli_query($connect, $s2);

	// tạo array lưu tất cả các chuyên ngành
    $majors = array();

    while($row=mysqli_fetch_array($query_major)){
        array_push($majors, $row['ten_chuyen_nganh']);
        // echo $row['id_SB'] . '-' . $row['sub_1'] . '-' . $row['sub_2'] . '-' . $row['sub_3'] . '<br>';
    }
?>

<?php 
    $s_0 = "SELECT * FROM teachers;";
	$query_0 = mysqli_query($connect, $s_0);
	// tạo array teachers (key-value) = (username-email)
	$teachers = array();
	while($row = mysqli_fetch_array($query_0)){
		// echo $row['username'] . '-' . $row['email'] . '<br>';
		$teachers[$row['username']] = $row['email'];
	}

	// đếm số lượng teacher trong bảng teachers
	$count_teacher = mysqli_num_rows($query_0);
	// echo $count_teacher;

	// print_r($teachers);

	// mặc định gán cho các giáo viên tập phân ngành rỗng
	$giaovien_phannganh_array = array();
	foreach($teachers as $teacher => $email){
		$giaovien_phannganh_array[$teacher] = array();
	}
	// $_SESSION['giaovien_phannganh_array'] = $giaovien_phannganh_array;
	// var_dump($giaovien_phannganh_array['huyen']);
?>

<?
?>


<?php 
	// xử lí phần thêm chuyên ngành cho giáo viên
	if(isset($_POST['add']) && $_SESSION['token'] == $_POST['_token']){
		// associative array luu giao vien va nganh phu trach
		// $giaovien_phannganh_array = isset($_SESSION['giaovien_phannganh_array']) ? $_SESSION['giaovien_phannganh_array'] : array();
		$giaovien_phannganh_array = isset($_SESSION['giaovien_phannganh_array']) ? $_SESSION['giaovien_phannganh_array'] : $giaovien_phannganh_array;
		$nganh_add_at_row = $_POST['tohop_add'];
		if($nganh_add_at_row == ''){
			echo "nganh_add_at_row empty";
		} else {
			$teacher_username = $_POST['teacher_username'];
			$teacher_email = $_POST['teacher_email'];
			$nganh_duoc_phan = isset($giaovien_phannganh_array[$teacher_username]) ? $giaovien_phannganh_array[$teacher_username] : array();
			// echo $nganh_phu_trach . '-' . $teacher_username . '-' . $teacher_email;

			// neu giao vien chua co nganh duoc phan thi tao moi
			if (!isset($giaovien_phannganh_array[$teacher_username])) {
                $giaovien_phannganh_array[$teacher_username] = array();
            }
    
            // thêm chuyên ngành được phân vào array với giáo viên tương ứng, nếu chuyên ngành đó chưa có 
            if(!in_array($nganh_add_at_row, $giaovien_phannganh_array[$teacher_username])){
                array_push($giaovien_phannganh_array[$teacher_username], $nganh_add_at_row);
            }
            
            // Cập nhật session
            $_SESSION['giaovien_phannganh_array'] = $giaovien_phannganh_array;

			print_r($giaovien_phannganh_array[$teacher_username]);
			echo "<br>";
		}
	}

	// xử lí phần xóa chuyên ngành đã được phần của giáo viên tương ứng
	if(isset($_POST['delete']) && $_SESSION['token'] == $_POST['_token']){
		// $giaovien_phannganh_array = isset($_SESSION['giaovien_phannganh_array']) ? $_SESSION['giaovien_phannganh_array'] : array();
		$giaovien_phannganh_array = isset($_SESSION['giaovien_phannganh_array']) ? $_SESSION['giaovien_phannganh_array'] : $giaovien_phannganh_array;
		$nganh_delete_at_row = $_POST['tohop_delete'];
		if($nganh_delete_at_row == ''){
			echo "nganh_delete_at_row empty";
		} else {
			$teacher_username = $_POST['teacher_username'];
			$teacher_email = $_POST['teacher_email'];
			$nganh_duoc_phan = isset($giaovien_phannganh_array[$teacher_username]) ? $giaovien_phannganh_array[$teacher_username] : array();

			// index của ngành muốn xóa
			$index = array_search($nganh_delete_at_row, $nganh_duoc_phan);

			if ($index !== false) {
				// xóa phần tử tại index (phần tử nganh_delete_at_row)
				unset($nganh_duoc_phan[$index]);
				
				// đặt lại các chỉ số của mảng nganh_duoc_phan
				$nganh_duoc_phan = array_values($nganh_duoc_phan);
				
				// cập nhật lại mảng chuyennganh_array
				$giaovien_phannganh_array[$teacher_username] = $nganh_duoc_phan;
				
				// lưu vào session
				$_SESSION['giaovien_phannganh_array'] = $giaovien_phannganh_array;
			}


			// echo $nganh_delete_at_row . '-' . $teacher_username . '-' . $teacher_email;
			print_r($giaovien_phannganh_array[$teacher_username]);
			echo "<br>";
		}
	}

	if(isset($_POST['save']) && $_SESSION['token'] == $_POST['_token']){
		$giaovien_phannganh_array = isset($_SESSION['giaovien_phannganh_array']) ? $_SESSION['giaovien_phannganh_array'] : $giaovien_phannganh_array;
		$teacher_username = $_POST['teacher_username'];
		$teacher_email = $_POST['teacher_email'];
		$nganh_duoc_phan = isset($giaovien_phannganh_array[$teacher_username]) ? $giaovien_phannganh_array[$teacher_username] : array();
		$s3 = "SELECT * FROM teachers WHERE username = '$teacher_username';";
		$query3 = mysqli_query($connect, $s3);
		$id_teacher = mysqli_fetch_array($query3)['id_teacher'];

		$id_nganh_array = array();
		foreach($nganh_duoc_phan as $nganh){
			$s4 = "SELECT * FROM ;";
		}

		// echo $id_teacher;
	}
?>

<?php 
	if(isset($_SESSION['giaovien_phannganh_array'])) {
		$giaovien_phannganh_array = $_SESSION['giaovien_phannganh_array'];
		// print_r($giaovien_phannganh_array);
	}
	// print_r($giaovien_phannganh_array['hong12']);
?>

<?php
	foreach($giaovien_phannganh_array as $giaovien => $nganh_duoc_phan_array){
		// echo $giaovien . '<br>';
	}
?>


<div style="display: flex; justify-content: center;">

<?php 
    if(isset($_SESSION['role'])) {
		include './layouts/menu.php';
	}
?>

<div class="container" style="display: block; width: 100%;">
	
<?php 
    if($count_teacher > 0){
        echo '<div class="container_body">
                <form action="" method="post">
                    <table>
                        <th>STT</th>
                        <th>Tài khoản</th>
                        <th>Email</th>
                        <th>Ngành phụ trách xét tuyển</th>
                        <th>Add</th>
                        <th>Delete</th>
						<th>Save</th>'
                        ?>
                        <?php
                            foreach($teachers as $teacher => $email){
                                    echo  '<tr id="id_' . $j . '">';
                                        echo '<td>' . $j . '</td>';
                                        echo '<td>' . $teacher . '</td>';
                                        echo '<td>' . $email . '</td>';

										?>
										<?php
										foreach($giaovien_phannganh_array as $giaovien => $nganh_duoc_phan_array){
											if($teacher == $giaovien){
												if(!empty($nganh_duoc_phan_array)){
													$nganh_duoc_phan_string = implode(' - ', $nganh_duoc_phan_array);
													echo '<td>' . $nganh_duoc_phan_string . '</td>';
												} else {
													echo '<td></td>';
												}
											}
										}

										?>

										<?php
                                        echo '<td>
											<form method="post" class="form_add" method="post">
												<input type="hidden" name="teacher_username" value="' . $teacher . '">
												<input type="hidden" name="teacher_email" value="' . $email . '">
												<select name="tohop_add" class="select_tohop">
													<option></option>';?>
													<?php foreach($majors as $major) { ?>
														<?php echo '<option value="' . $major .'">' . $major . '</option>'?>
													<?php } ?>
												<?php	
												echo '</select>';
												echo '<button type="submit" class="btn btn-primary" name="add">Click</button>';
												echo ' <input type="hidden" name="_token" value="'?><?php echo $token .'"/>' ?>
												<?php $_SESSION['token'] = $token; 
										echo '</form>';
										echo '</td>';


                                        echo '<td>
											<form class="form_delete" method="post">
												<input type="hidden" name="teacher_username" value="' . $teacher . '">
												<input type="hidden" name="teacher_email" value="' . $email . '">
												<select name="tohop_delete" class="select_tohop">
													<option></option>';?>
													<?php foreach($majors as $major) { ?>
														<?php echo '<option value="' . $major .'">' . $major . '</option>'?>
													<?php } ?>
												<?php
												echo '</select>';
												echo '<button type="submit" class="btn btn-danger" name="delete">Click</button>';
												echo ' <input type="hidden" name="_token" value="'?><?php echo $token .'"/>' ?>
												<?php $_SESSION['token'] = $token; 
										echo '</form>';
										echo '</td>';
										


                                        echo '<td>
												<form class="form_save"  action="" method="post">
													<input type="hidden" name="teacher_username" value="' . $teacher . '">
													<input type="hidden" name="teacher_email" value="' . $email . '">
													<button type="submit" class="btn btn-warning" name="save">Save</button>';
                                                	echo ' <input type="hidden" name="_token" value="'?><?php echo $token .'"/>' ?>
                                                	<?php $_SESSION['token'] = $token; 
										echo '	</form>
											</td>';
                                        
                                        $j+=1;
                                    echo '</tr>';
                            }
                    ?>
                    </table>
                </form>
            </div>
<?php 
    } else {
        ?>
        <h2 style="text-align: center; margin-top: 30px;">Không có giáo viên nào !</h2>
        <?php
    }
?>
	<?php 
		include './layouts/footer.php';
	?>
</div>