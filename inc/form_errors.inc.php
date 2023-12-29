<?php
/********** เริ่มการแสดงผลข้อผิดพลาดที่เกิดจากความไม่ถูกต้องของข้อมูลจาก FORM **********/
/*
โดยตรวจสอบจากตัวแปร $FORM_ERRORS ว่าได้ถูกกำหนดค่าไว้แล้วหรือยัง
ซึ่งตัวแปร $FORM_ERRORS นี้จะมีชนิดเป็น array
ประกอบไปด้วย key ของฟิลด์ที่เกิดความผิดพลาด
และมี value เป็น error message ที่เราได้กำหนดไว้ก่อนหน้านี้ (ใน post.php และ view.php)
*/
if (isset($FORM_ERRORS)):
?>
<div class="alert alert-danger">
	<ul class="list-unstyled">
		<?php
		/********** เริ่ม LOOP แสดงผล errors **********/
		/*
		วนลูปทุก value ใน array $FORM_ERRORS
		โดยกำหนดแต่ละ value ให้อยู่ในตัวแปร $message
		*/
		foreach ($FORM_ERRORS as $message):
		?>
		<li>
			<span class="glyphicon glyphicon-exclamation-sign"></span>
			<?php
			echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
			?>
		</li>
		<?php
		endforeach;
		/********** จบ LOOP แสดงผล errors **********/
		?>
	</ul>
</div>
<?php
endif;
/********** จบการแสดงผลข้อผิดพลาดที่เกิดจากความไม่ถูกต้องของข้อมูลจาก FORM **********/
