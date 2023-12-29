<?php
/********** เริ่ม FORM ตั้งกระทู้ใหม่ **********/
/*
โดย form นี้จะใช้ method POST ในการส่งข้อมูลไปยัง post.php
ข้อมูลที่จะส่งให้กับ post.php ก็ได้แก่
title เป็น input type=text
description เป็น textarea
และ name เป็น input type=text
*/
?>
<form action="post.php" method="post" class="form-horizontal panel panel-default">
	<div class="panel-heading">
		<h4>
			<span class="glyphicon glyphicon-pencil"></span>
			ตั้งกระทู้ใหม่
		</h4>
	</div>
	<div class="panel-body">
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
			<label for="titleInput" class="col-sm-2 control-label">*หัวข้อ</label>
			<div class="col-sm-10">
				<input
					type="text"
					id="titleInput"
					name="title"
					value="<?php
					echo htmlspecialchars($DATA['title'], ENT_QUOTES, 'UTF-8');
					?>"
					placeholder="หัวข้อ"
					spellcheck="false"
					class="form-control"
				>
			</div>
		</div>
		<div class="form-group <?php
		/*
		ถ้ามี key ชื่อ 'description' อยู่ใน array $FORM_ERRORS
		ให้เพิ่ม class 'has-error' เข้าไปใน <div> นี้
		*/
		if (isset($FORM_ERRORS['description'])) {
			echo 'has-error';
		}
		?>">
			<label for="descriptionTextarea" class="col-sm-2 control-label">*รายละเอียด</label>
			<div class="col-sm-10">
				<textarea
					id="descriptionTextarea"
					name="description"
					rows="10"
					placeholder="รายละเอียด"
					spellcheck="false"
					class="form-control"
				><?php
				echo htmlspecialchars($DATA['description'], ENT_QUOTES, 'UTF-8');
				?></textarea>
			</div>
		</div>
		<div class="form-group <?php
		/*
		ถ้ามี key ชื่อ 'name' อยู่ใน array $FORM_ERRORS
		ให้เพิ่ม class 'has-error' เข้าไปใน <div> นี้
		*/
		if (isset($FORM_ERRORS['name'])) {
			echo 'has-error';
		}
		?>">
			<label for="nameInput" class="col-sm-2 control-label">*ชื่อ</label>
			<div class="col-sm-4">
				<input
					type="text"
					id="nameInput"
					name="name"
					value="<?php
					echo htmlspecialchars($DATA['name'], ENT_QUOTES, 'UTF-8');
					?>"
					placeholder="ชื่อ"
					spellcheck="false"
					class="form-control"
				>
			</div>
		</div>
		<hr>
		<div class="form-group">
			<div class="col-sm-4 col-sm-offset-4">
				<button type="submit" class="btn btn-primary btn-block">
					ตั้งกระทู้
				</button>
			</div>
		</div>
	</div>
</form>
<?php
/********** จบ FORM แสดงความเห็น **********/
