<?php
		/*
		แสดง errors (ถ้ามี)
		ดูคำอธิบายใน inc/form_errors.inc.php
		*/
		require 'inc/form_errors.inc.php';
		?>
		<div class="form-group <?php
		/*
		ถ้ามี key ชื่อ 'title' อยู่ใน array $FORM_ERRORS
		ให้เพิ่ม class 'has-error' เข้าไปใน <div> นี้
		*/
		if (isset($FORM_ERRORS['title'])) {
			echo 'has-error';
		}
        
?>">


<form class="form-horizontal" action="login.php" method="post">
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
        <div class="col-sm-10">
            <input type="email" class="form-control" id="email" name="email" placeholder="Email">
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">เข้าสูู่ระบบ</button>
        </div>
    </div>
</form>