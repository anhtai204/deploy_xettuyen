<?php 
		include './layouts/header.php';
?>

<div style="display: flex; justify-content: center;">

<?php 
    if(isset($_SESSION['role'])) {
		include './layouts/menu.php';
	}
?>

<div style="display: block; width: 100%;">
	
	<!-- Page content -->


	<?php 
		include './layouts/footer.php';
	?>
</div>