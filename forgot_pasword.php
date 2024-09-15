<?php 
    include './layouts/header.php'
?>

<?php 
    if(isset($_POST['submit'])) {
        $email = $_POST['email'];
    }
?>

<form action="" method="post">
    <label for="">Nhập email đã đăng ký</label>
    <input type="email" name="email">
    <input type="submit" class="btn btn-primary" value="Send" name="submit"/>
</form>

<?php 
    include './layouts/footer.php'
?>