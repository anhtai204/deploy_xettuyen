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
	// hàm insert vào bảng phannganh_giaovien nếu cặp dữ liệu (major_id, teacher_id) không bị trùng lặp
	function insertIfNotExist($major_id, $teacher_id){

        $connect = new mysqli('localhost', 'root', '', 'xettuyen');

        // Câu lệnh SQL để tạo và gọi stored procedure
        $sql = "
            DROP PROCEDURE IF EXISTS insert_if_not_exist;
            CREATE PROCEDURE insert_if_not_exist(
                IN major_id_param INT,
                IN teacher_id_param INT
            )
            BEGIN
                IF NOT EXISTS (
                    SELECT 1
                    FROM phannganh_giaovien
                    WHERE id_major = major_id_param AND id_teacher = teacher_id_param
                ) THEN
                    INSERT INTO phannganh_giaovien (id_major, id_teacher)
                    VALUES (major_id_param, teacher_id_param);
                END IF;
            END;
        ";

		if (mysqli_multi_query($connect, $sql)) {
            // đảm bảo rằng tất cả các kết quả từ các câu lệnh SQL được xử lý và bộ nhớ được giải phóng đúng cách
            do {
                //  lưu trữ kết quả của câu lệnh SQL hiện tại và trả về một đối tượng mysqli_result
                if ($result = $connect->store_result()) {
					// giải phóng bộ nhớ được sử dụng bởi đối tượng mysqli_result
                    $result->free();
                }
				//  $connect->more_results() : kiểm tra xem có còn kết quả khác từ các câu lệnh SQL chưa được xử lý không
				// $connect->next_result(): di chuyển đến kết quả tiếp theo để xử lý nếu có nhiều câu lệnh SQL
            } while ($connect->more_results() && $connect->next_result());

            // gọi stored procedure
            $insert_command = "CALL insert_if_not_exist($major_id, $teacher_id);";
            mysqli_query($connect, $insert_command);
            echo "insert success";
        } else {
            echo "insert failed";
        }
    }
?>


<?php 
	// lấy ra các chuyên ngành đã được xét tổ hợp xét tuyển
    $s2 = "SELECT * FROM majors;";
    $query_major = mysqli_query($connect, $s2);

	// tạo array lưu tất cả các chuyên ngành
    $majors = array();

    while($row=mysqli_fetch_array($query_major)){
        array_push($majors, $row['major_name']);
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

?>


<?php 
	// tạo 1 array lưu id_teacher và tập hợp các id_major trong bảng phannganh_giaovien
	$idTeacher_listIdMajor_array = array();
	$s5 = "SELECT * FROM teachers;";
	$query5 = mysqli_query($connect, $s5);
	while($row1 = mysqli_fetch_array($query5)){
		$id_teacher = $row1['id_teacher'];
		$s6 = "SELECT * FROM phannganh_giaovien WHERE id_teacher = '$id_teacher';";
		$query6 = mysqli_query($connect, $s6);
		$listMajor = array();
		while($row2 = mysqli_fetch_array($query6)){
			array_push($listMajor, $row2['id_major']);
			$idTeacher_listIdMajor_array[$id_teacher] =  $listMajor;
		}
	}	

	// tạo 1 array lưu id_major - major tương ứng trong bảng majors
	$idMajor_major_array = array();
	$s6 = "SELECT * FROM majors;";
	$query6 = mysqli_query($connect, $s6);
	while($row3 = mysqli_fetch_array($query6)){
		$idMajor_major_array[$row3['id_major']] = $row3['major_name'];
	}
	// print_r($idMajor_major_array);

	// tạo 1 array lưu id_teacher và username_teacher tương ứng trong bảng teachers
	$idTeacher_username_array = array();
	$s7 = "SELECT * FROM teachers;";
	$query7 = mysqli_query($connect, $s7);
	while($row4 = mysqli_fetch_array($query7)){
		$idTeacher_username_array[$row4['id_teacher']] = $row4['username'];
	}
	// print_r($idTeacher_username_array);

	// tạo 1 array lưu username và list major tương ứng
	$username_listMajor_array = array();
	foreach($idTeacher_listIdMajor_array as $id_teacher => $listIdMajor){
		$listMajor = array();
		foreach($listIdMajor as $id_major){
			array_push($listMajor, $idMajor_major_array[$id_major]);
		}
		$username_listMajor_array[$idTeacher_username_array[$id_teacher]] = $listMajor;
	}
	// print_r($username_listMajor_array);
?>


<?php
	// xử lí phần thêm chuyên ngành cho giáo viên
	if(isset($_POST['add']) && $_SESSION['token'] == $_POST['_token']){
		if($_POST['tohop_add'] != '') {
			$teacher_username = $_POST['teacher_username'];
			$major_at_row = $_POST['tohop_add'];
			// echo $teacher_username . '-' . $teacher_email . '-' . $major_at_row;
	
			// lấy ra id teacher tương ứng trong bảng teachers
			$s1 = "SELECT * FROM teachers WHERE username='$teacher_username';";
			$query1 = mysqli_query($connect, $s1);
			$teacher_id = mysqli_fetch_array($query1)['id_teacher'];
			// echo 'teacher id: ' . $teacher_id;
			
			// lấy ra id major tương ứng trong bảng majors
			$s2 = "SELECT * FROM majors WHERE major_name='$major_at_row';";
			$query2 = mysqli_query($connect, $s2);
			$major_id = mysqli_fetch_array($query2)['id_major'];
			// echo 'major id : ' . $major_id;
	
			// lưu teacher_id va và major_id vào bảng phannganh_gv
			// $s3 = "CALL insert_if_not_exist($major_id, $teacher_id);";
			// mysqli_query($connect, $s3);

			insertIfNotExist($major_id, $teacher_id);

			header("location: admin_phan_nganh_gv.php");

		}
	}
?>	



<?php
	// xử lí phần xóa chuyên ngành
	if(isset($_POST['delete']) && $_SESSION['token'] == $_POST['_token']){
		if($_POST['tohop_delete'] != '') {
			$teacher_username = $_POST['teacher_username'];
			$major_at_row = $_POST['tohop_delete'];

			// xóa id_major tương ứng trong bảng phannganh_giaovien
			$id_teacher = array_search($teacher_username, $idTeacher_username_array);
			$id_major = array_search($major_at_row, $idMajor_major_array);

			// echo $id_teacher . '-' . $id_major;
			$s8 = "DELETE FROM phannganh_giaovien WHERE id_teacher = '$id_teacher' AND id_major = '$id_major';";
			mysqli_query($connect, $s8);

			header("location: admin_phan_nganh_gv.php");
		}
	}
?>

<?php
	// xử lí phần lưu chuyên ngành
	if(isset($_POST['save']) && $_SESSION['token'] == $_POST['_token']){
		$teacher_username = $_POST['teacher_username'];

		// Kiểm tra xem $teacher_username có tồn tại trong mảng không
		if (isset($username_listMajor_array[$teacher_username])) {
			// list major của username tương ứng
			$listMajors = $username_listMajor_array[$teacher_username];
			// $string_major_list = implode(" - ", $listMajors);
			if (is_array($listMajors)) {
				$idMajor_array = array();
				foreach($listMajors as $major){
					array_push($idMajor_array, array_search($major, $idMajor_major_array));
				}
				$id_major_list_array = implode(" - ", $idMajor_array);
				
				$s9 = "UPDATE teachers SET major_id_list = '$id_major_list_array' WHERE username = '$teacher_username';";
				mysqli_query($connect, $s9);
			}
		} else {
			$s10 = "UPDATE teachers SET major_id_list = '' WHERE username = '$teacher_username';";
			mysqli_query($connect, $s10);
		}
	}
?>


<?php
	// $hideSaveButton = false;

	// Nếu major_id_list trong bảng teachers khác với tập các major trong phân ngành thì hiển thị nút save -> Modify
	foreach($teachers as $teacher => $email){
		$s9 = "SELECT * FROM teachers WHERE username = '$teacher';";
		$query9 = mysqli_query($connect, $s9);
		$list_id_major_in_teachers_table_string = mysqli_fetch_array($query9)['major_id_list'];
		if($list_id_major_in_teachers_table_string == ''){
			$list_id_major_in_teachers_table_array = array();
		} else {
			$list_id_major_in_teachers_table_array = explode(" - ", $list_id_major_in_teachers_table_string);
		}

		// Tìm id_teacher dựa trên username, nếu teacher_id tồn tại trong bảng teachers vả tồn tại mảng
		$teacher_id = array_search($teacher, $idTeacher_username_array);
		if ($teacher_id !== false && isset($idTeacher_listIdMajor_array[$teacher_id])) {
			$list_id_major_in_phannganh_gv_table = $idTeacher_listIdMajor_array[$teacher_id];
		} else {
			$list_id_major_in_phannganh_gv_table = [];
		}

		// Đảm bảo rằng các biến là mảng
		if (!is_array($list_id_major_in_teachers_table_array)) {
			$list_id_major_in_teachers_table_array = [];
		}
		if (!is_array($list_id_major_in_phannganh_gv_table)) {
			$list_id_major_in_phannganh_gv_table = [];
		}

		sort($list_id_major_in_teachers_table_array);
		sort($list_id_major_in_phannganh_gv_table);

		// print_r($list_id_major_in_teachers_table_array);
		// print_r($list_id_major_in_phannganh_gv_table);

		// var_dump($list_id_major_in_teachers_table_array == $list_id_major_in_phannganh_gv_table);
		// echo "<br>";
		if ($list_id_major_in_teachers_table_array !== $list_id_major_in_phannganh_gv_table) {
			
		}
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
											foreach($username_listMajor_array as $username => $listMajor){
												if($username == $teacher){
													if(!empty($listMajor)){
														$majors_string = implode(" - ", $listMajor);
														echo '<td>' . $majors_string . '</td>';
													} 
												}
											}
											// nếu teacher không nằm trong bảng phannganh_giaovien thì hiển thị cặp thẻ td
											echo in_array($teacher, array_keys($username_listMajor_array)) ? "" : '<td></td>';
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
										


                                        

										$s9 = "SELECT * FROM teachers WHERE username = '$teacher';";
										$query9 = mysqli_query($connect, $s9);
										$list_id_major_in_teachers_table_string = mysqli_fetch_array($query9)['major_id_list'];
										// lấy ra major_id_list từ bảng teachers nếu rỗng thì sẽ thành Array([0]=> ) vì vậy cần chuyển thành array rỗng
										if($list_id_major_in_teachers_table_string == ''){
											$list_id_major_in_teachers_table_array = array();
										} else {
											$list_id_major_in_teachers_table_array = explode(" - ", $list_id_major_in_teachers_table_string);
										}
								
										// Tìm id_teacher dựa trên username, nếu teacher_id tồn tại trong bảng teachers vả tồn tại mảng
										$teacher_id = array_search($teacher, $idTeacher_username_array);
										if ($teacher_id !== false && isset($idTeacher_listIdMajor_array[$teacher_id])) {
											$list_id_major_in_phannganh_gv_table = $idTeacher_listIdMajor_array[$teacher_id];
										} else {
											$list_id_major_in_phannganh_gv_table = [];
										}
								
										// Đảm bảo rằng các biến là mảng
										if (!is_array($list_id_major_in_teachers_table_array)) {
											$list_id_major_in_teachers_table_array = [];
										}
										if (!is_array($list_id_major_in_phannganh_gv_table)) {
											$list_id_major_in_phannganh_gv_table = [];
										}
								
										// sắp xếp trước khi so sánh
										sort($list_id_major_in_teachers_table_array);
										sort($list_id_major_in_phannganh_gv_table);

										echo "<br>";
										if ($list_id_major_in_teachers_table_array !== $list_id_major_in_phannganh_gv_table) {
											echo '<td>
											<form class="form_save" method="post">
												<input type="hidden" name="teacher_username" value="' . $teacher . '">
												<button type="submit" id="btn_save" class="btn btn-warning" name="save">Save</button>
												<input type="hidden" name="_token" value="'?><?php echo $token .'"/>' ?>
												<?php $_SESSION['token'] = $token; 
											echo '</form>';
											echo '</td>';
										} else {
											// echo '<td></td>';
											echo '<td> <button type="submit" id="btn_save" class="btn btn-success" name="">Already save</button> </td>';
										}
                                        
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