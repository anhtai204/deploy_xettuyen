<?php 
		include './layouts/header.php';
?>

<div style="display: flex; justify-content: center;">

<?php 
    if(isset($_SESSION['role'])) {
		include './layouts/menu.php';
	}
?>

<?php

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

        // Thực thi câu lệnh SQL
        if ($connect->multi_query($sql)) {
            // Đọc tất cả kết quả từ các câu lệnh SQL
            do {
                // Lấy kết quả
                if ($result = $connect->store_result()) {
                    $result->free();
                }
            } while ($connect->more_results() && $connect->next_result());

            // Gọi stored procedure
            $insert_command = "CALL insert_if_not_exist($major_id, $teacher_id);";
            mysqli_query($connect, $insert_command);
            echo "success";
        } else {
            echo "Error executing queries: " . $connect->error;
        }
    }

    insertIfNotExist(1, 1);
?>


<div style="display: block; width: 100%;">
	
	<!-- Page content -->


	<?php 
		include './layouts/footer.php';
	?>
</div>