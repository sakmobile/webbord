<?php
require 'inc/mysqli.inc.php';
session_start();
/*
เราใช้ view.php เป็นทั้งไฟล์ที่ทำการแสดงผลและบันทึกข้อมูลความเห็นใหม่
ดังนั้นเราจะตรวจสอบว่าการเรียกไฟล์นี้นั้นเป็นการบันทึกหรือไม่ด้วยค่าของตัวแปร $_SERVER['REQUEST_METHOD']
ซึ่งมันจะมีค่าเป็น 'POST' หากมีการ submit มาจาก <form> ที่มี method="post"
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	/*
	ตรวจสอบให้แน่ชัดว่ามีข้อมูลที่จำเป็นส่งมาครบหรือไม่ด้วย isset()
	ซึ่งจะเป็นจริงหากใน $_POST มี key ที่ต้องการครบ
	*/
	if (!isset($_POST['topic_id'], $_POST['description'], $_POST['name'])) {
		/*
		หากไม่ครบก็ให้ redirect ไปที่ index.php
		*/
		header('Location: index.php');
		exit;
	}
	/*
	เราจะ copy $_POST มาไว้ในตัวแปร $DATA
	ด้วยเหตุผลที่ว่าเราจะไม่เปลี่ยนแปลงค่าของ $_POST โดยตรง
	และเพื่อให้เป็นแนวทางเดียวกันกับทุกตัวแปรที่จะส่งไปยัง template
	*/
	$DATA = $_POST;
	/*
	$TOPIC_ID จะถูกใช้ใน template
	*/
	$TOPIC_ID = (int)$DATA['topic_id'];
	/*
	ทำการ trim() (ตัดช่องว่างหน้าและหลัง) ของข้อมูลใน $DATA ทุกตัว
	*/
	foreach ($DATA as $key => $value) {
		$DATA[$key] = trim($value);
	}
	/*
	ตรวจสอบว่ามีกระทู้ที่มี id ตาม $TOPIC_ID อยู่จริงหรือไม่
	จะเห็นว่าเราสามารถนำ $TOPIC_ID ไปแทนที่ตรงๆ ได้เลย ไม่ใช้ mysqli::escape_string()
	เพราะก่อนหน้านี้เรากำหนด $TOPIC_ID ด้วย (int)$DATA['topic_id']
	ซึ่ง (int) เป็น cast operator จะทำการแปลงค่าใน $DATA['topic_id'] ให้เป็นตัวเลขจำนวนเต็ม
	หากไม่สามารถแปลงได้ มันจะให้ค่า 0 เสมอ
	*/
	$result = $mysqli->query(
		"
		SELECT `id`
		FROM `topic`
		WHERE `id` = {$TOPIC_ID}
		LIMIT 1
		"
	);
	/*
	หาก SELECT ไม่เจอ mysqli_result::fetch_row() จะ return null
	*/
	if (!$result->fetch_row()) {
		header('Location: index.php');
		exit;
	}
	/*
	ทำการตรวจสอบความถูกต้องของข้อมูลแบบเดียวกันกับ post.php
	*/
	if ($DATA['description'] === '') {
		$FORM_ERRORS['description'] = "กรุณาระบุ 'ข้อความ'";
	} elseif (mb_strlen($DATA['description'], 'UTF-8') > 65535) {
		$FORM_ERRORS['description'] = "'ข้อความ' ต้องมีความยาวไม่เกิน 65535 ตัวอักษร";
	}
	if ($DATA['name'] === '') {
		$FORM_ERRORS['name'] = "กรุณาระบุ 'ชื่อ'";
	} elseif (mb_strlen($DATA['name'], 'UTF-8') > 64) {
		$FORM_ERRORS['name'] = "'ชื่อ' ต้องมีความยาวไม่เกิน 64 ตัวอักษร";
	}
	/*
	ถ้าไม่มีตัวแปร $FORM_ERRORS ถูกสร้างขึ้นมาจากการตรวจสอบข้างต้น แสดงว่าไม่มี error
	ข้อมูลทั้งหมดสามารถ INSERT เข้าฐานข้อมูลได้อย่างปลอดภัย
	*/
	if (!isset($FORM_ERRORS)) {
		$mysqli->query(
			/*
			mysqli::escape_string() จะแปลงตัวอักษรพิเศษ เช่น ' ให้เป็น \' หรือ ''
			ซึ่งทำให้ MySQL Server รู้ว่ามันเป็นข้อมูล ไม่ใช่ delimeter
			หากเราไม่ใช้ mysqli::escape_string() และผ่านข้อมูลไปเป็น SQL query โดยตรง
			อาจจะทำให้เกิด error หรือ SQL Injection ขึ้นได้
			ยกเว้น $TOPIC_ID ตามที่กล่าวไว้ข้างต้น
			และ $_SERVER['REMOTE_ADDR'] ที่เชื่อถือได้ว่าไม่มีตัวอักษรพิเศษแน่นอน
			และนี่คือข้อดีของการใช้ mysqli ในแบบ OOP คือจะเห็นว่าเราสามารถแทนที่
			$mysqli->escape_string() ลงไปใน double quote string ได้เลย
			แต่ถ้าเราใช้ mysqli_escape_string() ที่เป็น procedural style
			จะไม่สามารถทำแบบนี้ได้
			*/
			"
			INSERT INTO `comment`
			(
				`topic_id`,
				`description`,
				`name`,
				`ip`
			)
			VALUES
			(
				{$TOPIC_ID},
				'{$mysqli->escape_string($DATA['description'])}',
				'{$mysqli->escape_string($DATA['name'])}',
				'{$_SERVER['REMOTE_ADDR']}'
			)
			"
		);
		/*
		อ่าน id ของความเห็นที่เพิ่ง INSERT เข้าไปด้วย mysqli::$insert_id
		*/
		$comment_id = $mysqli->insert_id;
		/*
		ทำการ UPDATE กระทู้
		โดยให้เวลาตอบกระทู้ล่าสุด (last_commented) เป็นเวลาปัจจุบัน เพื่อให้กระทู้ย้ายขึ้นมาบนสุด
		และเพิ่มจำนวนความเห็น (num_comments)
		และกำหนดชื่อผู้แสดงความเห็นล่าสุด (last_commented_name) เป็น $DATA['name']
		*/
		$mysqli->query(
			"
			UPDATE `topic`
			SET
				`last_commented` = NOW(),
				`num_comments` = `num_comments` + 1,
				`last_commented_name` = '{$mysqli->escape_string($DATA['name'])}'
			WHERE `id` = {$TOPIC_ID}
			"
		);
		/*
		refresh view.php ด้วยการ redirect
		โดยกำหนด hash tag #comment-<id ของความเห็นล่าสุด> เข้าไปด้วย
		เพื่อให้ browser scroll มาที่ความเห็นที่ผู้ใช้เพิ่งบันทึกไป
		*/
		header("Location: view.php?topic_id={$TOPIC_ID}#comment-{$comment_id}");
		exit;
	}
	/*
	หากมี error ก็จะแสดงผลให้ผู้ใช้แก้ไขข้อมูลให้ถูกต้อง
	*/
} else {
	/*
	หากไม่ใช่การ POST ก็ให้กำหนดค่า default สำหรับ $TOPIC_ID จาก $_GET['topic_id']
	และกำหนดค่า default สำหรับ $DATA ซึ่งจะถูกใช้งานใน inc/view.inc.php
	โดยให้เป็นค่าว่างทั้งหมด
	*/
	$TOPIC_ID = empty($_GET['topic_id'])
		? 0
		: (int)$_GET['topic_id'];
	$DATA = array(
		'description' => '',
		'name' => '',
	);
}
/*
SELECT กระทู้ที่มี id ตาม $TOPIC_ID
*/
$result = $mysqli->query(
	"
	SELECT
		`id`,
		`created`,
		`title`,
		`description`,
		`name`,
		`ip`,
		`num_comments`,
		`num_views`
	FROM `topic`
	WHERE `id` = {$TOPIC_ID}
	LIMIT 1
	"
);
$topic = $result->fetch_assoc();
/*
หากไม่เจอกระทู้ที่มี id ตาม $TOPIC_ID ตัวแปร $topic ก็จะเป็น null
*/
if (!isset($topic)) {
	/*
	กำหนดให้ inc/main.inc.php แสดง error
	*/
	$FATAL_ERROR = $TITLE = "ไม่มีกระทู้หมายเลข {$TOPIC_ID} อยู่ในฐานข้อมูล";
	require 'inc/main.inc.php';
}
/*
กำหนดตัวแปร $ITEMS ให้เป็น array ของทั้งกระทู้และความเห็น
โดยเราจะไม่แยกมันออกจากกัน เพื่อลดความซ้ำซ้อนของโค้ดแสดงผล  (ดู inc/view.inc.php)
ให้ $topic เป็นสมาชิกตัวแรกของมัน
*/
$ITEMS = array($topic);
/*
ปล่อยหน่วยความจำที่ $result ใช้
*/
$result->free();
/*
UPDATE จำนวนผู้เข้าชมของกระทู้
*/
$mysqli->query(
	"
	UPDATE `topic`
	SET `num_views` = `num_views` + 1
	WHERE `id` = {$TOPIC_ID}
	LIMIT 1
	"
);
/*
หากกระทู้มีความเห็น
*/
if ($topic['num_comments']) {
	/*
	กำหนดค่า default ให้กับตัวแปร $PAGE
	*/
	$PAGE = empty($_GET['page'])
		? 1
		: (int)$_GET['page'];
	/*
	จำนวนความเห็นที่จะแสดงใน 1 หน้า
	*/
	$ITEMS_PER_PAGE = 100;
	/*
	คำนวณ offset เริ่มต้นที่จะกำหนดใน LIMIT ซึ่ง offset ของแถวแรกเริ่มที่ 0 ไม่ใช่ 1
	ถ้าอยู่ที่หน้า 1 ก็จะได้ LIMIT 0, 100
	ถ้าอยู่ที่หน้า 3 ก็จะได้ LIMIT 200, 100
	*/
	$START_OFFSET = ($PAGE - 1) * $ITEMS_PER_PAGE;
	/*
	SELECT และอ่านข้อมูลความเห็นเข้ามาไว้ใน $ITEMS (ดู index.php)
	*/
	$result = $mysqli->query(
		"
		SELECT
			`id`,
			`topic_id`,
			`created`,
			`description`,
			`name`,
			`ip`
		FROM `comment`
		WHERE `topic_id` = {$TOPIC_ID}
		ORDER BY `created`
		LIMIT {$START_OFFSET}, {$ITEMS_PER_PAGE}
		"
	);
	while ($comment = $result->fetch_assoc()) {
		$ITEMS[] = $comment;
	}
	$result->free();
	$result = $mysqli->query(
		"
		SELECT COUNT(*)
		FROM `comment`
		WHERE `topic_id` = {$TOPIC_ID}
		"
	);
	$FOUND_ROWS = current($result->fetch_row());
	$result->free();
	$NUM_PAGES = ceil($FOUND_ROWS / $ITEMS_PER_PAGE);
} else {
	/*
	ถ้าไม่มีความเห็น ก็กำหนดค่า default ให้กับ $NUM_PAGES เพื่อใช้ใน template ต่อไป
	*/
	$NUM_PAGES = 0;
}
/*
บอก inc/main.inc.php ให้ใช้หัวข้อกระทู้เป็น <title>
*/
$TITLE = $topic['title'];
/*
บอก inc/main.inc.php ให้ require ไฟล์ inc/view.inc.php เป็น template
*/
$PAGE_TEMPLATE = 'inc/view.inc.php';
require 'inc/main.inc.php';
