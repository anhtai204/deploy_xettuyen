<?php 
		include './layouts/header.php';
?>

<div style="display: flex; justify-content: center;">

<?php 
    if(isset($_SESSION['role'])) {
		include './layouts/menu.php';
	}
?>

<link rel="stylesheet" href="./assets/css/student_knowled_record.css">
<div style="display: block; width: 100%;">
	<div class="container">
		<div class="content">
			<p class="content-label">Thông tin cá nhân</p>
			<div>
				<label>Họ và tên:</label>
				<input disabled type="text" value="Phan Xuân Vinh">
			</div>
			<div>
				<label>Ngày sinh:</label>
				<input disabled type="date" value="12/03/2004">
			</div>
			<div>
				<label>Số điện thoại:</label>
				<input disabled type="tel" value="0345058548">
			</div>
			<div>
				<label>Giới tính:</label>
				<input disabled type="text" value="Nam">
			</div>
			<div>
				<label>Địa chỉ thường trú:</label>
				<input disabled type="text" value="Nghệ An">
			</div>
			<div>
				<label>Địa chỉ email:</label>
				<input disabled type="email" value="phanxuanvinh4592@gmail.com">
			</div>
		</div>
		<div>
			<p>Thông tin đào tạo</p>
			<div>
				<label>Trường:</label>
				<input disabled type="text" value="HNUE">
			</div>
			<div>
				<label>Địa chỉ trường:</label>
				<input disabled type="text" value="136 Xuân Thủy - Cầu Giấy - Hà Nội">
			</div>
			<div>
				<label>Toán</label>
				<input disabled type="number" value="10">
			</div>
			<div>
				<label>Ngữ văn</label>
				<input disabled type="number" value="10">
			</div>
			<div>
				<label>Tiếng Anh</label>
				<input disabled type="number" value="10">
			</div>
			<div>
				<label>Vật lý</label>
				<input disabled type="number" value="10">
			</div>
			<div>
				<label>Hóa học</label>
				<input disabled type="number" value="10">
			</div>
			<div>
				<label>Sinh học</label>
				<input disabled type="number" value="10">
			</div>
			<div>
				<label>Lịch sử</label>
				<input disabled type="number" value="10">
			</div>
			<div>
				<label>Địa lý</label>
				<input disabled type="number" value="10">
			</div>
			<div>
				<label>Tin học</label>
				<input disabled type="number" value="10">
			</div>
			<div>
				<label>Công nghệ</label>
				<input disabled type="number" value="10">
			</div>
			<div>
				<label>Giáo dục công dân</label>
				<input disabled type="number" value="10">
			</div>
			<div>
				<label>Giáo dục thể chất</label>
				<input disabled type="number" value="10">
			</div>
		</div>
	</div>

	<!-- Page content -->
    <!-- <?php
		$username = $_SESSION["taiKhoan"];
		$sql = "	SELECT * FROM academic_records as ar
						JOIN students as s ON s.id_student = ar.id_student
						WHERE s.username = '$username';";
		$query = mysqli_query($connect, $sql);
		$result = mysqli_num_rows($query);
		if($result == 1) {

		}
	?> -->

	<?php 
		include './layouts/footer.php';
	?>
</div>